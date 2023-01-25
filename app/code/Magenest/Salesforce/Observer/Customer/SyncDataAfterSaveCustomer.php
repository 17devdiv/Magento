<?php

namespace Magenest\Salesforce\Observer\Customer;

use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;

/**
 * Class SyncDataAfterSaveCustomer
 * @package Magenest\Salesforce\Observer\Customer
 */
class SyncDataAfterSaveCustomer extends AbstractCustomer
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
        if (!$this->_isProcessed) {
            try {
                $event    = $observer->getEvent();
                if($event->getName() == 'customer_address_save_after'){
                    /** @var $customerAddress Address */
                    $customerAddress = $observer->getCustomerAddress();
                    $customer = $customerAddress->getCustomer();
                }else{
                    $customer = $event->getCustomer();
                }
                if ($customer instanceof Customer) {
                    $this->syncLead($customer);
                    $this->syncAccount($customer);
                    $this->syncContact($customer);
                    $this->_isProcessed = true;
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }
}
