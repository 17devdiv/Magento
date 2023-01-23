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

namespace Dotsquares\Gdpr\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Replace implements ObserverInterface
{
    private $helper;

    public function __construct(
        \Dotsquares\Gdpr\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function execute(Observer $observer)
    {
        $allow = $this->helper->getConfig('cusotmerdelete/cookies/cookies_enable');
        if($allow == 1){
            $block = $observer->getLayout()->getBlock('google_analytics');
            if ($block) {
                $block->setTemplate('Dotsquares_Gdpr::googleanalytics/ga.phtml');
            }
        }
    }
}