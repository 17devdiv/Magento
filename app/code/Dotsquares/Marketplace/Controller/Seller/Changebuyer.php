<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Seller;

/**
 * This class contains changing customer to seller functions
 */
class Changebuyer extends \Magento\Framework\App\Action\Action {
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {
        /**
         * Get Customer Session Datas
         * 
         * @var int (id)
         */
        
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        /**
         * Load Page Layout
         */
        if ($customerSession->isLoggedIn () && $status == 0) {
            $this->_view->loadLayout ();
            $this->_view->renderLayout ();
        } else {
            
            $this->_redirect ( 'marketplace/seller/login' );
        }
    }
}
