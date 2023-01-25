<?php

namespace Magenest\Salesforce\Model;

use Magenest\Salesforce\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Cron
 * @package Magenest\Salesforce\Model
 */
class Cron
{
    /**
     * @var Sync\Contact
     */
    protected $_contact;

    /**
     * @var Sync\Lead
     */
    protected $_lead;

    /**
     * @var Sync\Account
     */
    protected $_account;

    /**
     * @var Sync\Order
     */
    protected $_order;

    /**
     * @var Sync\Product
     */
    protected $_product;

    /**
     * @var Sync\Campaign
     */
    protected $_campaign;

    /**
     * @var Sync\Opportunity
     */
    protected $_opportunity;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Cron constructor.
     * @param Sync\Contact $contact
     * @param Sync\Campaign $campaign
     * @param Sync\Account $account
     * @param Sync\Lead $lead
     * @param Sync\Order $order
     * @param Sync\Opportunity $opportunity
     * @param Sync\Product $product
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helper
     */
    public function __construct(
        Sync\Contact $contact,
        Sync\Campaign $campaign,
        Sync\Account $account,
        Sync\Lead $lead,
        Sync\Order $order,
        Sync\Opportunity $opportunity,
        Sync\Product $product,
        ScopeConfigInterface $scopeConfig,
        Data $helper
    ) {
        $this->_account     = $account;
        $this->_contact     = $contact;
        $this->_campaign    = $campaign;
        $this->_lead        = $lead;
        $this->_order       = $order;
        $this->_opportunity = $opportunity;
        $this->_product     = $product;
        $this->scopeConfig  = $scopeConfig;
        $this->helper       = $helper;
    }

    /**
     * Get Config Value
     *
     * @param $type
     * @return int
     */
    protected function getSyncConfigTime($type)
    {
        return $this->getEnabledSyncConfig($type) ? $this->helper->getSyncConfigTime($type) : 0;
    }

    /**
     * @param string $type
     * @return bool
     */
    protected function getEnabledSyncConfig($type)
    {
        return $this->helper->getEnabledSyncConfig($type);
    }

    /**
     * sync all queued data to Salesforce
     * maximum 250 items at a time
     */
    public function syncData()
    {
        $realTimeMinute = (int)date('i');
        $realTimeHour   = (int)date('h');
        if ($time = $this->getSyncConfigTime('contact')) {
            if ($time != 0 && $this->calculateTime($time, $realTimeMinute, $realTimeHour)) {
                $this->_contact->syncAllQueue();
            }
        }

        if ($time = $this->getSyncConfigTime('lead')) {
            if ($time != 0 && $this->calculateTime($time, $realTimeMinute, $realTimeHour)) {
                $this->_lead->syncAllQueue();
            }
        }

        if ($time = $this->getSyncConfigTime('account')) {
            if ($time != 0 && $this->calculateTime($time, $realTimeMinute, $realTimeHour)) {
                $this->_account->syncAllQueue();
            }
        }

        if ($time = $this->getSyncConfigTime('product')) {
            if ($time != 0 && $this->calculateTime($time, $realTimeMinute, $realTimeHour)) {
                $this->_product->syncAllQueue();
            }
        }

        if ($time = $this->getSyncConfigTime('order')) {
            if ($time != 0 && $this->calculateTime($time, $realTimeMinute, $realTimeHour)) {
                $this->_order->syncAllQueue();
            }
        }

        if ($time = $this->getSyncConfigTime('opportunity')) {
            if ($time != 0 && $this->calculateTime($time, $realTimeMinute, $realTimeHour)) {
                $this->_opportunity->syncAllQueue();
            }
        }

        if ($time = $this->getSyncConfigTime('campaign')) {
            if ($time != 0 && $this->calculateTime($time, $realTimeMinute, $realTimeHour)) {
                $this->_campaign->syncAllQueue();
            }
        }
    }

    /**
     * Calculate time
     *
     * @param $time
     * @param $minute
     * @param $hour
     * @return bool
     */
    protected function calculateTime($time, $minute, $hour)
    {
        /** change minute 0 to minute 60th */
        if ($minute == 0) {
            $minute = 60;
        }

        return ($minute % $time == 0) || ($time == 120 && $hour % 2 == 0);
    }
}
