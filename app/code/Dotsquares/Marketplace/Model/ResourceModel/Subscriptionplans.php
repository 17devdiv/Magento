<?php

/**

 *
 * @category     Dotsquares
 * @package      Dotsquares_Marketplace
 * @version      3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Model\ResourceModel;

/**
 * This class initiates seller subscription plans model primary id
 */
class Subscriptionplans extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
    /**
     * Define main table
     */
    protected function _construct() {
        $this->_init ( 'marketplace_subscription_plans', 'id' );
    }
}