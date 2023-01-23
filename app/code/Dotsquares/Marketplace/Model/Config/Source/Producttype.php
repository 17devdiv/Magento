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
class Producttype implements \Magento\Framework\Option\ArrayInterface {
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return [
                [
                        'value' => 'simple',
                        'label' => __ ( 'Simple' )
                ],
                [
                        'value' => 'virtual',
                        'label' => __ ( 'Virtual' )
                ],
                [
                        'value' => 'configurable',
                        'label' => __ ( 'Configurable' )
                ] ,
                [
                        'value' => 'downloadable',
                        'label' => __ ( 'Downloadable' )
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
                0 => __ ( 'Simple' ),
                1 => __ ( 'Virtual' ),
                2 => __ ( 'Configurable' ),
3=>__('Downloadable')
        ];
    }
}
