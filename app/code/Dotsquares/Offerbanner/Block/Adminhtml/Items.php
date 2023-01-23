<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */
namespace Dotsquares\Offerbanner\Block\Adminhtml;

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
        $this->_headerText = __('Banner Images');
        $this->_addButtonLabel = __('Add New Item');
        parent::_construct();
    }
}
