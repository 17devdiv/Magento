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
 * This class contains the seller tr
 */
class Transactions extends \Magento\Framework\App\Action\Action {
    /**
     * Function to load seller store page
     *
     * @return $array
     */
    public function execute() {
        /**
         * Check whether module enabled or not
         */
        $checkingForModule = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' )->getModuleEnable ();
        if ($checkingForModule) {
            $logedInUser = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            $customerId = $logedInUser->getId ();
            $loggedUserObject = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
            $status = $loggedUserObject->load ( $customerId, 'customer_id' )->getStatus ();
            if ($logedInUser->isLoggedIn () && $status == 1) {
                $transactionId = $this->getRequest ()->getParam ( 'id' );
                if (! empty ( $transactionId )) {
                    $this->updateAcknowledgeForTransaction ( $customerId, $transactionId );
                }
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($logedInUser->isLoggedIn () && $status == 0) {
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
    
    /**
     * To update acknowledge for seller transaction
     *
     * @param int $customerId            
     * @param int $transactionId            
     *
     * @return void
     */
    public function updateAcknowledgeForTransaction($customerId, $transactionId) {
        /**
         * Getting seller payment by id
         */
        $sellerPayments = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Payments' )->load ( $transactionId );
        /**
         * Checking for seller payment count
         */
        if (count ( $sellerPayments ) >= 1) {
            /**
             * Get seller id form seller payment model
             */
            $sellerId = $sellerPayments->getSellerId ();
            /**
             * Checking for seller payments
             */
            if ($customerId == $sellerId) {
                /**
                 * Getting date
                 */
                $date = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
                /**
                 * Setting data to seller payments
                 */
                $sellerPayments->setIsAck ( 1 );
                $sellerPayments->setAckAt ( $date );
                $sellerPayments->save ();
                /**
                 * Seting session message for seller
                 */
                $this->messageManager->addSuccess ( __ ( 'The payment has been updated successfully.' ) );
            }
        }
    }
}
