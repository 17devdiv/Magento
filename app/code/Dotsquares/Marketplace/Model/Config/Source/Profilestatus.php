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
class Profilestatus implements \Magento\Framework\Option\ArrayInterface {
    const PENDING = 0;
    const ACTIVE = 1;
    const COMPLETE = 2;
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray() {
        return [ 
                static::PENDING => __ ( 'Pending' ),
                static::ACTIVE => __ ( 'Active' ),
                static::COMPLETE => __ ( 'Complete' ) 
        ];
    }
}
