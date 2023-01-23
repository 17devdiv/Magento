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
 * This class contains order item functions
 */
class Item extends \Magento\Framework\App\Action\Action {
    
    /**
     * Declare result page factory
     */
    protected $resultPageFactory;
    
    /**
     * Declare message manager
     */
    protected $messageManager;
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        parent::__construct ( $context );
    }
    /**
     * Customer cance, return and refund funcationaity
     *
     * @return void
     */
    public function execute() {
        /**
         * Get order details from query string
         */
        $orderId = $this->getRequest ()->getParam ( 'order_id' );
        $sellerId = $this->getRequest ()->getParam ( 'seller_id' );
        $productId = $this->getRequest ()->getParam ( 'product_id' );
        $action = trim ( $this->getRequest ()->getParam ( 'action' ) );
        $reason = $this->getRequest ()->getParam ( 'reason' );
       
        /**
         * Create order object
         */
        $order = $this->_objectManager->get ( 'Magento\Sales\Model\Order' )->load ( $orderId );
        $orderCustomerId = $order->getCustomerId ();
        
        /**
         * Get customer data
         */
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        /**
         * Checking whether order id is equal to customer id
         */
        if ($orderCustomerId == $customerId && $customerId != '') {
            /**
             * Get seller order items
             */
            $sellerOrderItems = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Orderitems' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'product_id', $productId )->getFirstItem ();
            
            /**
             * Change order item status
             */
            if (count ( $sellerOrderItems ) >= 1) {
                $orderItems = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Orderitems' )->load ( $sellerOrderItems->getId () );
                
                /**
                 * Update cancel
                 */
                if ($action == 'cancel') {
                    $orderItems->setIsBuyerCanceled ( 1 );
                    $orderItems->save ();
                }
                /**
                 * Update refund
                 */
                if ($action == 'refund') {
                    $orderItems->setIsBuyerRefunded ( 1 );
                    $orderItems->save ();
                }
                /**
                 * Update return
                 */
                if ($action == 'return') {
                    $orderItems->setIsBuyerReturned ( 1 );
                    $orderItems->save ();
                }
            }
            
            /**
             * Email receiver and sender details
             */
            $sellerData = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $sellerId );
            $receiverInfo = [ 
                    'name' => $sellerData->getName (),
                    'email' => $sellerData->getEmail () 
            ];
            
            $senderInfo = [ 
                    'name' => $customerSession->getCustomer ()->getName (),
                    'email' => $customerSession->getCustomer ()->getEmail () 
            ];
            
            $templateId = 'marketplace_order_item_request_template';
            /**
             * Set email tempalate variables
             */
            $emailTemplateVariables = array ();
            $emailTemplateVariables ['receivername'] = $sellerData->getName ();
            $emailTemplateVariables ['requesttype'] = ucfirst ( $action );
            $emailTemplateVariables ['requestperson'] = 'Buyer';
            $emailTemplateVariables ['requestperson_name'] = $customerSession->getCustomer ()->getName ();
            $emailTemplateVariables ['requestperson_email'] = $customerSession->getCustomer ()->getEmail ();
            $emailTemplateVariables ['increment_id'] = $order->getIncrementId ();
            $emailTemplateVariables ['reason'] = $reason;
            $emailTemplateVariables ['product_id'] = $productId;
            $emailTemplateVariables ['seller_id'] = $sellerId;
            $emailTemplateVariables ['order_id'] = $orderId;
            
            /**
             * Get store id
             */
            $nullId = null;
            $storeManager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
            $store = $storeManager->getStore ( $nullId );
            $storeId = $store->getStoreId ();
            /**
             * To assign email template variable for request url
             */
            $emailTemplateVariables ['requesturl'] = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ( $storeId )->getBaseUrl () . 'marketplace/order/vieworder/id/' . $orderId;
            /**
             * Sending email notification
             */
            $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTemplateVariables, $senderInfo, $receiverInfo, $templateId );
            
            /**
             * Setting success message
             */
            $this->messageManager->addSuccess ( ucfirst ( $action ) . ' ' . __ ( 'request has been sent to seller successfully' ) );
            /**
             * Redirect to order detail page
             */
            $this->_redirect ( 'sales/order/view/order_id/' . $orderId );
        } else {
            /**
             * Setting success message
             */
            $this->messageManager->addError ( __ ( 'You dont have permission to proceed this operation' ) );
            /**
             * To redirect to customer account page
             */
            $this->_redirect ( 'customer/account' );
        }
    }
}
