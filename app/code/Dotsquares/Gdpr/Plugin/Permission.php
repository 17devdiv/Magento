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

use Magento\Newsletter\Model\Subscriber as MageSubscriber;
use \Magento\Framework\App\Request\Http;

class Permission
{
    public function __construct(
        Http $request,
        \Magento\Newsletter\Model\SubscriberFactory $subscriber
    ) {
        $this->request = $request;
        $this->subscriberFactory = $subscriber;
    }

    public function aroundSendConfirmationSuccessEmail(MageSubscriber $oSubject, callable $proceed){
		$post = $this->request->getParams();
        $email = $oSubject->getEmail();
        $gdprstatus = $oSubject->getOthermails();
        $newsletter = $this->subscriberFactory->create()->loadByEmail($email);
        $gdprstatus = $newsletter->getOthermails();
        if((array_key_exists('othermails',$post) && (array_key_exists('is_subscribed',$post)))){
            $returnValue = $proceed();
            return $returnValue;
        }elseif($gdprstatus == 0){
            return $this;
        }else{
            $returnValue = $proceed();
            return $returnValue;
        }
    }
}