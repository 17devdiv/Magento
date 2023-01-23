<?php
/**

 *
 * @category    Dotsquares
 * @package    Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Subscriptionplans\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class for seller subscription plans form functions
 */
class Form extends Generic {
    /**
     *Function to prepare form
     * @return $this
     */
    protected function _prepareForm() {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create ( [ 
                'data' => [ 
                        'id' => 'edit_form',
                        'action' => $this->getData ( 'action' ),
                        'method' => 'post' 
                ] 
        ] );
        $form->setUseContainer ( true );
        $this->setForm ( $form );
        
        return parent::_prepareForm ();
    }
}