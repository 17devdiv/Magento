<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Dotsquares\Quickcontact\Block\Adminhtml\Items\View\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Main extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_dotsquares_quickcontact_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Name'), 'title' => __('Name')]
        );
		$fieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => __('Email'), 'title' => __('Email')]
        );
		$fieldset->addField(
            'phone',
            'text',
            ['name' => 'phone', 'label' => __('Phone'), 'title' => __('Phone')]
        );
		$fieldset->addField(
            'message',
            'text',
            ['name' => 'message', 'label' => __('Message'), 'title' => __('Message')]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
