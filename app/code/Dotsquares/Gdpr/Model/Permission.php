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

namespace Dotsquares\Gdpr\Model;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\Request\Http;

class Permission implements ObserverInterface
{
    public function __construct(
        Http $request,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriber
    ) {
        $this->request = $request;
        $this->customer = $customer;
        $this->subscriberFactory = $subscriber;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer_id = $observer->getEvent()->getCustomer()->getId();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->create('Magento\Customer\Model\Customer')->load($customer_id)->setWebsiteterms(1)->save();
		$newsletter = $this->subscriberFactory->create()->loadByCustomerId($customer_id);
        $post = $this->request->getParams();
		if((array_key_exists('othermails',$post)) && (array_key_exists('is_subscribed',$post))){
            $newsletter->setOthermails(1)->save();
        }else if(array_key_exists('othermails',$post)){
			$customer = $objectManager->create('Magento\Customer\Model\Customer')->load($customer_id);
			$customer->setNewslettermail(1);
			$customer->save();
		}else{
			$customer = $objectManager->create('Magento\Customer\Model\Customer')->load($customer_id);
			$customer->setNewslettermail(0);
			$customer->save();
		}
    }
}