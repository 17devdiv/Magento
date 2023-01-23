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
 * This class contains transactions functions
 * @author user
 *
 */
class Transactions extends Container {
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        /**
         * To assign controller for grid
         */
        $this->_controller = 'adminhtml_transactions';
        /**
         * To assign block group
         */
        $this->_blockGroup = 'Dotsquares_Marketplace';
        /**
         * To assign header text
         */
        $this->_headerText = __ ( 'Manage Seller Orders' );
    }
}