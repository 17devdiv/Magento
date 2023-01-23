<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */
namespace Dotsquares\Quickcontact\Block\Adminhtml\Items;

class View extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
     protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_items';
        $this->_blockGroup = 'Dotsquares_Quickcontact';
          $this->_mode = 'view'; 
        parent::_construct();

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        //$this->setId('quickcontact_items_view');

    }
}
