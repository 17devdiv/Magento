<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Sync;

use Magenest\Salesforce\Model\Sync;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class Order
 * @package Magenest\Salesforce\Controller\Adminhtml\Sync
 */
class Order extends Action
{
    /**
     * @var Sync\Order
     */
    protected $_order;

    /** @var \Magento\Sales\Model\OrderFactory  */
    protected $_orderFactory;

    /**
     * Customer constructor.
     *
     * @param Context $context
     * @param Sync\Order $order
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        Sync\Order $order,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_order = $order;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $orderIncrementId = $this->getRequest()->getParam('id');
            if ($orderIncrementId) {
                $orderId = $this->_orderFactory->create()->loadByIncrementId($orderIncrementId)->getId();
                $this->_order->sync($orderIncrementId);
                $this->messageManager->addSuccess(
                    __('Order sync process complete, check out Reports for result.')
                );
            } else {
                $this->messageManager->addNotice(
                    __('No order has been selected')
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something happen during syncing process. Detail: ' . $e->getMessage())
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (isset($orderId)) {
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } else {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Salesforce::config_salesforce');
    }
}