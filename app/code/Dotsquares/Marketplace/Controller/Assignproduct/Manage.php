<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Assignproduct;

/**
 * This class contains manage assign product functions
 */
class Manage extends \Magento\Framework\App\Action\Action {
    protected $dataHelper;
    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Dotsquares\Marketplace\Helper\Data $dataHelper            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load manage assign products layout
     *
     * @return $array
     */
    public function execute() {
        $this->checkSellerEnabledorNot ();
    }
    /**
     * Check Module Enabled or Not
     */
    public function checkSellerEnabledorNot() {
        $customer = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customer->getId ();
        $seller = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $sellerStatus = $seller->load ( $customerId, 'customer_id' )->getStatus ();
        /**
         * Checking whether module enable or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        $enableAssignProduct = $this->dataHelper->getAssignProduct ();
        if ($moduleEnabledOrNot) {
            if ($customer->isLoggedIn () && $sellerStatus == 1 && $enableAssignProduct == 1) {
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customer->isLoggedIn () && $sellerStatus == 0) {
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                $this->_redirect ( 'marketplace/product/manage' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
}
