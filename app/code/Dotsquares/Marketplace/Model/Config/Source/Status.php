<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Model\Config\Source;

/**
 * This class contains seller subscription status functions
 */
class Status implements \Magento\Framework\Option\ArrayInterface {
    const ENABLED = 1;
    const DISABLED = 0;
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray() {
        return [ 
                static::ENABLED => __ ( 'Enabled' ),
                static::DISABLED => __ ( 'Disabled' ) 
        ];
    }
}
