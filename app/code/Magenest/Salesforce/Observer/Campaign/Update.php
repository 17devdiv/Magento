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

namespace Magenest\Salesforce\Observer\Campaign;

use Magenest\Salesforce\Helper\Data;
use Magenest\Salesforce\Logger\Logger;
use Magenest\Salesforce\Model\Config\Source\SyncMode;
use Magenest\Salesforce\Model\Queue;
use Magenest\Salesforce\Model\QueueFactory;
use Magenest\Salesforce\Observer\SyncObserver;
use Magento\Framework\Event\Observer;
use \Magenest\Salesforce\Model\Sync\Campaign;

/**
 * Class Update
 * @package Magenest\Salesforce\Observer\Campaign
 */
class Update extends SyncObserver
{
    /** @var bool */
    protected $_isProcessed = false;

    /** @var string */
    protected $type = 'campaign';

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_isProcessed) {
            try {
                if ($this->getEnabledSyncConfig()) {
                    /** @var \Magento\CatalogRule\Model\Rule $campaign */
                    $campaign = $observer->getEvent()->getRule();
                    if ($this->getSyncConfigMode() == SyncMode::ADD_TO_QUEUE) {
                        $this->addToQueue(Queue::TYPE_CAMPAIGN, $campaign->getId());
                    } else {
                        $id = $campaign->getId();
                        $this->_campaign->sync($id);
                    }
                }
                $this->_isProcessed = true;
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }
}
