<?php

namespace Dotsquares\Productfaq\Block\Adminhtml\Items\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    
    public function _construct()
    {
        parent::_construct();
        $this->setId('dotsquares_productfaq_items_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__("Product's Faq"));
    }
}
