<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml;
use Magento\Backend\Block\Widget\Grid\Container;
/**
 * This class contains seller payments grid
 */
class Payments extends Container {
    /**
     * Constructor Function 
     *
     * @return void
     */
    protected function _construct() {
        /**
         * Setup seller payments grid info
         */
        $this->_controller = 'adminhtml_payments';
        /**
         * Set block group
         */
        $this->_blockGroup = 'Dotsquares_Marketplace';
        /**
         * Set header text
         */
        $this->_headerText = __ ( 'Manage Seller Payments' );
        /**
         * Call parent class construct
         */
        parent::_construct ();
        /**
         * Removing add seller payment button
         */
        $this->removeButton ( 'add' );
    }
}