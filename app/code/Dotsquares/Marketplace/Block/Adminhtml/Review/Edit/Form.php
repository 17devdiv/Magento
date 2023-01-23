<?php
/**

 *
 * @category    Dotsquares
 * @package    Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Review\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
/**
 * This class contains form  functions for review edit feature
 * @author user
 *
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