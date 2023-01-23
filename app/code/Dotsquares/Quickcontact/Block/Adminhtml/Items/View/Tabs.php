<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */
namespace Dotsquares\Quickcontact\Block\Adminhtml\Items\View;

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
        $this->setId('dotsquares_quickcontact_items_view_tabs');
        $this->setDestElementId('view_form');
        $this->setTitle(__('Item'));
    }
}
