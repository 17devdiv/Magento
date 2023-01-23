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
 * This class contains subscription profiles info
 */
class Allpayments extends Container {
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        /**
         * Set grid data
         */
        $this->_controller = 'adminhtml_allpayments';
        $this->_blockGroup = 'Dotsquares_Marketplace';
        $this->_headerText = __ ( 'Seller Payments List' );
        $this->_addButtonLabel = __ ( 'Back' );
        parent::_construct ();
    }
}