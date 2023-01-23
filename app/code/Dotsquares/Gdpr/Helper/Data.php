<?php
/**
 * Dotsquares
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @author Dotsquares
 * @package Dotsquares_GDPR
 * @copyright Copyright (c) Dotsquares (https://www.dotsquares.com/)
 */

namespace Dotsquares\Gdpr\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
     public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $timestamp,
        \Magento\Sales\Model\OrderFactory $ordercollection,
        \Magento\Customer\Model\Customer $customerSession
    ) {
        $this->_orderFactory = $ordercollection;
        $this->_timestamp = $timestamp;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

	public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
	public function getOrdersize($customer_id ,$customer_email)
    {
        return $this->_orderFactory->create()->getCollection()->addFieldToFilter('customer_id', $customer_id)
		->addFieldToFilter('customer_email', $customer_email)->getSize();
    }
	
	public function getPendingorder($customer_id ,$customer_email)
    {
        return $this->_orderFactory->create()->getCollection()
		->addFieldToFilter('customer_id', $customer_id)
		->addFieldToFilter('customer_email', $customer_email)
		->addFieldToFilter('status', 'pending')->getSize();
    }
	
	public function getProcessingorder($customer_id ,$customer_email)
    {
        return $this->_orderFactory->create()->getCollection()
		->addFieldToFilter('customer_id', $customer_id)
		->addFieldToFilter('customer_email', $customer_email)
		->addFieldToFilter('status', 'processing')->getSize();
    }
	
	public function getCompletedorder($customer_id ,$customer_email)
    {
        return $this->_orderFactory->create()->getCollection()
		->addFieldToFilter('customer_id', $customer_id)
		->addFieldToFilter('customer_email', $customer_email)
		->addFieldToFilter('status', 'complete')->getSize();
    }
	
	public function getTimestamp()
    {
		return $this->_timestamp->gmtTimestamp();
    }
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }
}