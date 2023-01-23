<?php
namespace Dotsquares\Productfaq\Block\Adminhtml\Items\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('dotsquares_items_form');
        $this->setTitle(__("Product's Faq"));
    }

    public function _prepareForm()
    {
       
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('dotsquares_productfaq/items/save'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
