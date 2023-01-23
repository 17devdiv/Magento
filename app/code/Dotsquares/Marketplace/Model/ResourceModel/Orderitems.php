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
 * This class initiates orderitems model primary id
 */
class Orderitems extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    /**
     * Defines order items
     */
    protected function _construct() {
        $this->_init ( 'marketplace_sellerorderitems', 'id' );
    }
}
