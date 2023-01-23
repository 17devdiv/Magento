<?php

namespace Dotsquares\Productfaq\Block\Adminhtml;

class Items extends \Magento\Backend\Block\Widget\Grid\Container
{
    
    protected function _construct()
    {
        $this->_controller = 'items';
        $this->_headerText = __("Product's Faq");
        $this->_addButtonLabel = __('Add New Item');
        parent::_construct();
		$this->removeButton('add');
    }
}
