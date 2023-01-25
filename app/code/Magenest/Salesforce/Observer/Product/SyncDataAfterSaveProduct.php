<?php
namespace Magenest\Salesforce\Observer\Product;

use Magenest\Salesforce\Model\Config\Source\SyncMode;
use Magenest\Salesforce\Model\Queue;
use Magenest\Salesforce\Observer\SyncObserver;
use Magento\Framework\Event\Observer;

/**
 * Class Update
 * @package Magenest\Salesforce\Observer\Product
 */
class SyncDataAfterSaveProduct extends SyncObserver
{
    /**
     * @var bool
     */
    protected $_isProcessed = false;

    /**
     * @var string
     */
    protected $type = 'product';

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_isProcessed) {
            try {
                if ($this->getEnabledSyncConfig()) {
                    $event = $observer->getEvent();
                    /** @var  $product \Magento\Catalog\Model\Product */
                    $product = $event->getProduct();
                    if ($this->getSyncConfigMode() == SyncMode::ADD_TO_QUEUE) {
                        $this->addToQueue(Queue::TYPE_PRODUCT, $product->getId());
                    } else {
                        $id = $product->getId();
                        $this->_product->sync($id, true);
                    }
                }
                $this->_isProcessed = true;
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }
}
