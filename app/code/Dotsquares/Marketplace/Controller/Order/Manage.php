<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Order;

/**
 * This class contains manage seller order page
 */
class Manage extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var \Dotsquares\Marketplace\Helper\Data
     */
    protected $dataHelper;
    
    /**
     * Manage seller order construct
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Dotsquares\Marketplace\Helper\Data $dataHelper            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load recent orders in seller Dashboard
     *
     * @return $array
     */
    public function execute() {
        /**
         * Getting logged in user data
         */
        $customerSessionData = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSessionData->getId ();
        /**
         * Getting seller information
         */
        $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        /**
         * Checking for seller or not
         */
        $this->checkSellerOrNot ( $status, $customerSessionData );
    }
    /**
     * Function to check whether seller or not
     *
     * @return layout
     */
    public function checkSellerOrNot($status, $customerSessionData) {
        
        /**
         * Checking whether module enabled or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            /**
             * Checking for seller status
             */
            if ($customerSessionData->isLoggedIn () && $status == 1) {
                /**
                 * Load layout
                 */
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customerSessionData->isLoggedIn () && $status == 0) {
                /**
                 * Redirect to change buyer controller
                 */
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                /**
                 * Setting a session notice message
                 */
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                /**
                 * Redirect to seller login page
                 */
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
}
