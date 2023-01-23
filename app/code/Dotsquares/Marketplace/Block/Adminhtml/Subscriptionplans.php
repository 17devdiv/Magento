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
class Subscriptionplans extends Container {
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        /**
         * Setup subscription plans grid info
         */
        $this->_controller = 'adminhtml_subscriptionplans';
        $this->_blockGroup = 'Dotsquares_Marketplace';
        $this->_headerText = __ ( 'Manage Subscription' );
        parent::_construct ();
    }
}