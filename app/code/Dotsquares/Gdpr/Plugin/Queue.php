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

namespace Dotsquares\Gdpr\Plugin;

class Queue 
{
	public function __construct(
        \Magento\Newsletter\Model\Subscriber $subscriber
    ) {
        $this->_subscriber = $subscriber;
    }
	
    public function beforeaddSubscribersToQueue(\Magento\Newsletter\Model\ResourceModel\Queue $subject,\Magento\Newsletter\Model\Queue $queue, array $subscriberIds){
		foreach($subscriberIds as $subscriberId){
			$newsletter = $this->_subscriber->load($subscriberId);
			if($newsletter->getOthermails() == 1){
				$subscriberIdss[] = $subscriberId;
			}
		}
		$subscriberIds = $subscriberIdss;
		return [$queue, $subscriberIds];
    }
}