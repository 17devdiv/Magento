<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Salesforce extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_Salesforce
 * @author   ThaoPV
 */

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
use mysql_xdevapi\Exception;

/**
 * Class Create
 * @package Magenest\Salesforce\Observer\Order
 */
class Create extends SyncObserver
{
    /**
     * @var bool
     */
    protected $_isProcessed = false;

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            if(!$order instanceof \Magento\Sales\Model\Order){
                $orderIds = $observer->getEvent()->getOrderIds();
                if(is_array($orderIds) && !empty($orderIds)){
                    $orderId = reset($orderIds);
                    $order = $this->orderRepository->get($orderId);
                }
            }
            if($order instanceof \Magento\Sales\Model\Order || $order instanceof \Magento\Sales\Api\Data\OrderInterface){
                $orderState = $order->getState();
                if($orderState == \Magento\Sales\Model\Order::STATE_NEW ){
                    if ($this->getEnabledSyncConfig('order')) {
                        if ($this->getSyncConfigMode('order') == SyncMode::ADD_TO_QUEUE) {
                            $this->addToQueue(Queue::TYPE_ORDER, $order->getIncrementId());
                        } else {
                            $this->_order->sync($order->getIncrementId());
                        }
                    }
                    if ($this->getEnabledSyncConfig('opportunity')) {
                        if ($this->getSyncConfigMode('opportunity') == SyncMode::ADD_TO_QUEUE) {
                            $this->addToQueue(Queue::TYPE_OPPORTUNITY, $order->getIncrementId());
                        } else {
                            $this->_opportunity->sync($order->getIncrementId());
                        }
                    }
                }
                $this->_isProcessed = true;
            }else{
                throw new \Exception(__('Order not exist.'));
            }
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}
