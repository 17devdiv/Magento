<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Model\ResourceModel\Orderitems;

/**
 * This class contains orderitems model collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init ( 'Dotsquares\Marketplace\Model\Orderitems', 'Dotsquares\Marketplace\Model\ResourceModel\Orderitems' );
    }
}