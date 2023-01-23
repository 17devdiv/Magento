<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */
namespace Dotsquares\Enquiry\Block\Adminhtml;

class Items extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'items';
        $this->_headerText = __('Items');
        //$this->_addButtonLabel = __('Add New Item');
        parent::_construct();
		$this->removeButton('add');
    }
}
