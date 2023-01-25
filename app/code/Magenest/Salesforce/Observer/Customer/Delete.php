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

namespace Magenest\Salesforce\Observer\Customer;

use Magento\Framework\Event\Observer;

/**
 * Class Delete
 * @package Magenest\Salesforce\Observer\Customer
 */
class Delete extends AbstractCustomer
{
    /**
     * @var bool
     */
    protected $_isProcessed = false;

    /**
     * Admin delete a customer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_isProcessed) {
            try {
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = $observer->getEvent()->getCustomer();
                $email    = $customer->getEmail();
                if ($this->getEnabledSyncConfig('lead')) {
                    $this->_lead->delete($email);
                }

                if ($this->getEnabledSyncConfig('contact')) {
                    $this->_contact->delete($email);
                }
                $this->_isProcessed = true;
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
    }
}
