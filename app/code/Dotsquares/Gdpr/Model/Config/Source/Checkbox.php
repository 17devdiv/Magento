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

namespace Dotsquares\Gdpr\Model\Config\Source;
 
class Checkbox
{
    public function toOptionArray()
    {
        return [
            ['value' => 'customer', 'label'=>__('Customer\'s Account Delete')],
            //['value' => 'customerdata', 'label'=>__('Delete customer other data such as newsletter, reviews etc.')],
            ['value' => 'order', 'label'=>__('Customer\'s Order Data Anonymization')],
        ];
    }
}