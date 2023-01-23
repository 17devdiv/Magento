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

use Magento\Framework\Option\ArrayInterface;

class Select implements ArrayInterface
{
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $ret;
    }

    public function toArray()
    {
        $choose = [
            '1' => 'Newsletter Mail System',
            '2' => '3rd Party Mail System'
        ];
        return $choose;
    }
}