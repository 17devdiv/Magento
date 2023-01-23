<?php

namespace Dotsquares\Productfaq\Block\Adminhtml\Items\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    public function getTabLabel()
    {
        return __("Product's Faq");
    }
    public function getTabTitle()
    {
        return __("Product's Faq");
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_dotsquares_productfaq_items');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'product_name',
            'text',
            ['name' => 'product_name', 'label' => __('Product Name'), 'title' => __('Product Name'), 'readonly' => true, 'required' => false]
        );
        $fieldset->addField(
            'customer_name',
            'text',
            ['name' => 'customer_name', 'label' => __('Customer Name'), 'title' => __('Customer Name'), 'readonly' => true, 'required' => false]
        );
        $fieldset->addField(
            'customer_email',
            'text',
            ['name' => 'customer_email', 'label' => __('Customer Email'), 'title' => __('Customer Email'), 'readonly' => true,'required' => false]
        );
        $fieldset->addField(
            'created_date',
            'text',
            ['name' => 'created_date', 'label' => __('Created Date'), 'title' => __('Created Date'), 'readonly' => true,'required' => false]
        );
        $fieldset->addField(
            'question',
            'textarea',
            ['name' => 'question', 'label' => __('Question'), 'title' => __('Question'), 'readonly' => true,'required' => false]
        );
        $fieldset->addField(
            'answer',
            'textarea',
            ['name' => 'answer', 'label' => __('Answer'), 'title' => __('Answer'), 'required' => true]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'values' => array('-1'=>'Please Select..','1' => 'Enable','2' => 'Disable'),
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
