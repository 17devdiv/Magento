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

namespace Dotsquares\Gdpr\Plugin\Checkout;

use Magento\Checkout\Model\DefaultConfigProvider;

class CustomConfigProvider
{
    public function __construct(
        \Dotsquares\Gdpr\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function aftergetConfig(DefaultConfigProvider $subject,
        $result) {
		$gdpr_enable = $this->_helper->getConfig('cusotmerdelete/gdprlaw/checkout_enable');
		$gdprcheckobxmassage = $this->_helper->getConfig('cusotmerdelete/gdprlaw/gdprcheckobxmassage');
		$result['gdpr_enable'] = $gdpr_enable;
		$result['gdprcheckobxmassage'] = $gdprcheckobxmassage;
		return $result;
    }
}