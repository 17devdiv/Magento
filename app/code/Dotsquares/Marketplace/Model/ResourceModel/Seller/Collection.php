<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Model\ResourceModel\Seller;

/**
 * This class contains seller model collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init ( 'Dotsquares\Marketplace\Model\Seller', 'Dotsquares\Marketplace\Model\ResourceModel\Seller' );
    }
}