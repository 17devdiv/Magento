<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Model\ResourceModel;

/**
 * This class initiates order model primary id
 */
class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    /**
     * Define order
     */
    protected function _construct() {
        $this->_init ( 'marketplace_sellerorder', 'id' );
    }
}
