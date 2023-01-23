<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */
namespace Dotsquares\Offerbanner\Block\Adminhtml\Items\Edit;

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
        $this->setId('dotsquares_offerbanner_items_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Banner'));
    }
}
