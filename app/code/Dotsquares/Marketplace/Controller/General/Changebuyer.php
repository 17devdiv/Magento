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
 * This class contains customer to buyer funcationality
 */
class Changebuyer extends \Magento\Framework\App\Action\Action {
    /**
     * Funtion to change customer to seller layout
     *
     * @return layout
     */
    public function execute() {
        /**
         * Getting Customer Session
         * 
         * @param
         *            s customer Id(int)
         */
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        if ($customerSession->isLoggedIn () && $status == 0) {
            $this->_view->loadLayout ();
            $this->_view->renderLayout ();
        } elseif ($customerSession->isLoggedIn () && $status == 1) {
            $this->_redirect ( 'marketplace/seller/dashboard' );
        } else {
            $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
            $this->_redirect ( 'marketplace/seller/login' );
        }
    }
}
