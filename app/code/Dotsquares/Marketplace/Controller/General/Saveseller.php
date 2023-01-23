<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\General;

/**
 * This class contains save seller data functions
 */
class Saveseller extends \Magento\Framework\App\Action\Action {
    
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {
        $approvedConditions = $this->getRequest ()->getPost ( 'privacy_policy' );
        
        if ($approvedConditions == 1) {
           
            /**
             * Get customer object
             */
            $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            if ($customerSession->isLoggedIn ()) {
                $customerId = $customerSession->getId ();
                $customerObject = $customerSession->getCustomer ();
                $customerEmail = $customerObject->getEmail ();
                $product = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
                $sellerApproval = $product->getSellerApproval ();
                $customerGroupSession = $this->_objectManager->get ( 'Magento\Customer\Model\Group' );
                $customerGroupData = $customerGroupSession->load ( 'Marketplace Seller', 'customer_group_code' );
                $sellerGroupId = $customerGroupData->getId ();
                /**
                 * Checking customer approval or not
                 */
                if ($sellerApproval) {
                    $customerObject->setGroupId ( $sellerGroupId )->save ();
                  
                    $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
                    $sellerModel->setEmail ( $customerEmail )->setStatus ( 0 )->setCustomerId ( $customerId )->save ();
                } else {
                    $customerObject->setGroupId ( $sellerGroupId )->save ();
                    $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
                    $sellerModel->setEmail ( $customerEmail )->setStatus ( 1 )->setCustomerId ( $customerId )->save ();
                }
                $this->_redirect ( 'marketplace/general/changebuyer' );
            }
        }
        /**
         * Load page layout
         */
        $this->_view->loadLayout ();
        $this->_view->renderLayout ();
    }
}
