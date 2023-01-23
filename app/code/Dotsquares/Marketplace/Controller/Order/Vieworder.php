<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Order;

use Zend\Form\Annotation\Instance;

class Vieworder extends \Magento\Framework\App\Action\Action {
    
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * Seller over view
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory            
     *
     * @return Object
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $context->getMessageManager();
        parent::__construct ( $context );
    }
    
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {
       
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        if ($customerSession->isLoggedIn () && $status == 1) {
            
            /**
             * Get seller order collection
             */
            $orderId = $this->getRequest ()->getParam ( 'id' );
            $sellerOrderCollection = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Orderitems' )->getCollection ();
            $sellerOrderCollection->addFieldToFilter ( 'seller_id', $customerId );
            $sellerOrderCollection->addFieldToFilter ( 'order_id', $orderId );
            if (count ( $sellerOrderCollection ) >= 1) {
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } else {
                $this->messageManager->addNotice ( __ ( 'You dont have permission to access this page.' ) );
                $this->_redirect ( 'marketplace/order/manage' );
            }
        } else {
            $this->messageManager->addNotice ( __ ( 'You must have a seller account to access.' ) );
            $this->_redirect ( 'marketplace/seller/login' );
        }
    }
}
