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

namespace Magenest\Salesforce\Observer\Product;

use Magenest\Salesforce\Helper\Data;
use Magenest\Salesforce\Logger\Logger;
use Magenest\Salesforce\Model\QueueFactory;
use Magenest\Salesforce\Observer\SyncObserver;
use Magento\Framework\Event\Observer;
use Magenest\Salesforce\Model\Sync\Product;

/**
 * Class Delete
 * @package Magenest\Salesforce\Observer\Product
 */
class Delete extends SyncObserver
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
                    /** @var  $product \Magento\Catalog\Model\Product */
                    $product = $observer->getEvent()->getProduct();
                    $this->productRepository->deleteById($product->getSku());
                }
                $this->_isProcessed = true;
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }
}