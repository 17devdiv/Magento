<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */
namespace Dotsquares\Quickcontact\Block\Adminhtml\Items\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('dotsquares_quickcontact_items_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Item'));
    }
}
