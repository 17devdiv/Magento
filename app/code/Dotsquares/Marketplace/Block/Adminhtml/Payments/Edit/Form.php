<?php
/**

 *
 * @category    Dotsquares
 * @package    Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Payments\Edit;
use Magento\Backend\Block\Widget\Form\Generic;
/**
 * This class used for form container for payments
 */
class Form extends Generic {
    /**
     * Function to prepare form
     * @return object
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
        /**
         * Set form container
         */
        $form->setUseContainer ( true );
        /**
         * Set form
         */
        $this->setForm ( $form );
        /**
         * Return prepare form function
         */
        return parent::_prepareForm ();
    }
}