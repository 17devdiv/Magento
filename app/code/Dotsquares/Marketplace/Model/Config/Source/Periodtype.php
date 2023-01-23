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
class Periodtype implements \Magento\Framework\Option\ArrayInterface {
    const MONTH = 'month';
    const YEAR = 'year';
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray() {
        return [ 
                static::MONTH => __ ( 'Month' ),
                static::YEAR => __ ( 'Year' ) 
        ];
    }
    
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return [ 
                0 => __ ( 'Month' ),
                1 => __ ( 'Year' ) 
        ];
    }
}
