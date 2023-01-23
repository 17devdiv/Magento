<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Model\Config\Source;

/**
 * This class contains product type functions
 */
class Shippingtype implements \Magento\Framework\Option\ArrayInterface {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return [ 
                [ 
                        'value' => 'product',
                        'label' => __ ( 'Product' ) 
                ],
                [ 
                        'value' => 'store',
                        'label' => __ ( 'Store' ) 
                ] 
        ];
    }
    
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {
        return [ 
                0 => __ ( 'Product' ),
                1 => __ ( 'Store' ) 
        ];
    }
}