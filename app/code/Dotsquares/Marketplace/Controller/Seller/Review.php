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
 * This class contains the seller review section
 */
class Review extends \Magento\Framework\App\Action\Action {
    /**
     * Show all seller review
     */
    public function execute() {
        /**
         * Getting seller id for query param
         */
        $sellerId = $this->getRequest ()->getParam ( 'seller_id' );
        /**
         * Checking for seller id exist or not
         */
        if (empty ( $sellerId )) {
            /**
             * Creating object for logged in seller
             */
           
            $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            if (! $customerSession->isLoggedIn ()) {
                /**
                 * Redirect to customer login page
                 */
                $this->_redirect ( 'customer/account/login' );
                return;
            }
        }
        
        /**
         * Load seller review layout
         */
        $this->_view->loadLayout ();
        if ($this->getRequest ()->getParam ( 'seller_id' ) != '') {
            $this->_view->getLayout ()->unsetElement ( 'customer_account_navigation_block' );
        }
        $this->_view->renderLayout ();
    }
}