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
 * This class contains review grid
 */
class Review extends Container {
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        /**
         * To assign review grid data
         */
        $this->_controller = 'adminhtml_reviews';
        $this->_blockGroup = 'Dotsquares_Marketplace';
        $this->_headerText = __ ( 'Manage Seller Review' );
        parent::_construct ();
        /**
         * Removing add seller review button
         */
        $this->removeButton ( 'add' );
    }
}