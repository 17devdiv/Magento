<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Product;

class Manage extends \Magento\Framework\App\Action\Action {
    protected $dataHelper;
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
         * Getting user session
         */
        $customer = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        /**
         * Getting customer id
         */
        $customerId = $customer->getId ();
        /**
         * Getting seller data
         */
        $seller = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $sellerStatus = $seller->load ( $customerId, 'customer_id' )->getStatus ();
        
        /**
         * Checking whether module enable or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            /**
             * Checking whether seller or not
             */
            if ($customer->isLoggedIn () && $sellerStatus == 1) {
                /**
                 * Load layout for manage product
                 */
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customer->isLoggedIn () && $sellerStatus == 0) {
                /**
                 * Redirect to change buyer
                 */
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                /**
                 * Setting the notice session message
                 */
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                /**
                 * Redirect to login page
                 */
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            /**
             * Redirect to account page
             */
            $this->_redirect ( 'customer/account' );
        }
    }
}