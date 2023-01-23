<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * This class contains seller Status functions
 */
class Status implements ArrayInterface {
    const ENABLED = 1;
    const DISABLED = 0;
    
    /**
     * Function to get Options
     * 
     * @return array
     */
    public function toOptionArray() {
        return [ 
                static::ENABLED => __ ( 'Approved' ),
                static::DISABLED => __ ( 'Disapproved' ) 
        ];
    }
}
