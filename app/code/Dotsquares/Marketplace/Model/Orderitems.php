<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * This class initiates order items model
 */
class Orderitems extends AbstractModel {
    /**
     * Define resource model
     */
    protected function _construct() {
        $this->_init ( 'Dotsquares\Marketplace\Model\ResourceModel\Orderitems' );
    }
}