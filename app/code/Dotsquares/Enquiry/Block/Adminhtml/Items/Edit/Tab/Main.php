<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Dotsquares\Enquiry\Block\Adminhtml\Items\Edit\Tab;

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
        $model = $this->_coreRegistry->registry('current_dotsquares_enquiry_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
		
		$getdatasval = $model->getData();	
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($getdatasval['client_id']);			
		
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Reason for Enquiry'), 'title' => __('Reason for Enquiry'), 'required' => true, 'readonly' => true, 'style'   => 'opacity:1',]
        ); 
		
		$fieldset->addField(
            'dotsquares_number',
            'text',
            ['dotsquares_number' => 'dotsquares_number', 'label' => __('Order Number '), 'title' => __('Order Number '), 'required' => true, 'readonly' => true, 'style'   => 'opacity:1',]
        );
		
		$fieldset->addField(
            'enquiry_content',
            'textarea',
            ['textarea' => 'textarea', 'label' => __('Enquiry'), 'title' => __('Enquiry'), 'required' => true, 'readonly' => true, 'style'   => 'opacity:1',]
        ); 
		
		$fieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => __('Client Email Id'), 'title' => __('Client Email Id'), 'required' => true, 'readonly' => true, 'style'   => 'opacity:1',]
        )->setAfterElementHtml("
                         <script type=\"text/javascript\">
						 
						 require(['jquery', 'jquery/ui'], function($){ 
								  $(document).ready(function(){
														 //alert('functicon');
														 
														document.getElementById('item_email').value = '".$customer->getEmail()."';
														 
													 });  
							 });
							 </script>"); 
		
		
		$fieldset->addField(
            'created_at',
            'text',
            ['created_at' => 'created_at', 'label' => __('Created At'), 'title' => __('Created At'), 'required' => true, 'readonly' => true, 'style'   => 'opacity:1',]
        );
		
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
