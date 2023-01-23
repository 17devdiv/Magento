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
class Isack implements \Magento\Framework\Option\ArrayInterface {
    const YES = 1;
    const NO = 0;
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray() {
        return [ 
                static::YES => __ ( 'Yes' ),
                static::NO => __ ( 'No' ) 
        ];
    }
}
