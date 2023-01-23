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
 * This class contains product container functions
 * @author user
 *
 */
class Products extends Container {
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        $this->_controller = 'adminhtml_products';
        $this->_blockGroup = 'Dotsquares_Marketplace';
        $this->_headerText = __ ( 'Manage Products' );
    }
}