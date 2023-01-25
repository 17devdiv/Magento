<?php
namespace Magenest\Salesforce\Observer\Customer;

use Magenest\Salesforce\Model\Config\Source\SyncMode;
use Magenest\Salesforce\Model\Queue;
use Magenest\Salesforce\Observer\SyncObserver;
use Magento\Customer\Model\Customer;

/**
 * Class AbstractCustomer
 * @package Magenest\Salesforce\Observer\Customer
 */
abstract class AbstractCustomer extends SyncObserver
{
    /**
     * @param Customer $customer
     * @throws \Exception
     */
    public function syncContact($customer)
    {
        if ($this->getEnabledSyncConfig('contact')) {
            if ($this->getSyncConfigMode('contact') == SyncMode::ADD_TO_QUEUE) {
                /** add to queue mode */
                $this->addToQueue(Queue::TYPE_CONTACT, $customer->getId());
            } else {
                /** auto sync mode */
                $this->_contact->sync($customer->getId(), true);
            }
        }
    }

    /**
     * @param Customer $customer
     * @throws \Exception
     */
    public function syncLead($customer)
    {
        if ($this->getEnabledSyncConfig('lead')) {
            if ($this->getSyncConfigMode('lead') == SyncMode::ADD_TO_QUEUE) {
                /** add to queue mode */
                $this->addToQueue(Queue::TYPE_LEAD, $customer->getId());
            } else {
                /** auto sync mode */
                $this->_lead->sync($customer->getId(), true);
            }
        }
    }

    /**
     * @param Customer $customer
     * @throws \Exception
     */
    public function syncAccount($customer)
    {
        if ($this->getEnabledSyncConfig('account')) {
            if ($this->getSyncConfigMode('account') == SyncMode::ADD_TO_QUEUE) {
                /** add to queue mode */
                $this->addToQueue(Queue::TYPE_ACCOUNT, $customer->getId());
            } else {
                /** auto sync mode */
                $this->_account->sync($customer->getId(), true);
            }
        }
    }
}
