<?php
namespace Magenest\Salesforce\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Newsletter\Model\Subscriber;
use Magenest\Salesforce\Observer\SyncObserver;

/**
 * Class SyncSubscriberAsLead
 * @package Magenest\Salesforce\Observer\Customer
 */
class SyncSubscriberAsLead extends SyncObserver
{
    /** @var bool */
    protected $_isProcessed = false;

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_isProcessed) {
            try {
                if ($this->helper->getEnabledSyncConfig('subscriber')) {
                    /** @var Subscriber $subscriber */
                    $subscriber = $observer->getEvent()->getSubscriber();
                    if ($subscriber->getCustomerId() == 0) {
                        $this->_lead->syncByEmail($subscriber->getEmail());
                    }
                }
                $this->_isProcessed = true;
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }
}
