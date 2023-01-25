<?php

namespace Magenest\Salesforce\Observer\Order;

use Magenest\Salesforce\Helper\Data;
use Magenest\Salesforce\Logger\Logger;
use Magenest\Salesforce\Model\Config\Source\SyncMode;
use Magenest\Salesforce\Model\Queue;
use Magenest\Salesforce\Model\QueueFactory;
use Magenest\Salesforce\Observer\SyncObserver;
use Magento\Framework\Event\Observer;
use Magenest\Salesforce\Model\Sync\Order;
use Magenest\Salesforce\Model\Sync\Opportunity;

/**
 * Class UpdateAddress
 * @package Magenest\Salesforce\Observer\Order
 */
class UpdateAddress extends SyncObserver
{
    /** @var bool */
    protected $_isProcessed = false;

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->_isProcessed) {
            try {
                $order_id = $observer->getEvent()->getOrderId();
                $order    = $this->orderRepository->get($order_id);
                if ($this->getEnabledSyncConfig('order')) {
                    if ($this->getSyncConfigMode('order') == SyncMode::ADD_TO_QUEUE) {
                        $this->addToQueue(Queue::TYPE_ORDER, $order->getIncrementId());
                    } else {
                        $this->_order->sync($order->getIncrementId());
                    }
                }
                $this->_isProcessed = true;
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }
}