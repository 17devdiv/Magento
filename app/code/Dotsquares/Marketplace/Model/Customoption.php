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
 * This class initiates seller model
 */
class Customoption extends AbstractModel {
    /**
     * Define resource model
     */
    protected function _construct() {
        $this->_init ( 'Dotsquares\Marketplace\Model\ResourceModel\Customoption' );
    }
}