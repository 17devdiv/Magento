<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Seller\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Dotsquares\Marketplace\Model\System\Config\Status;

/**
 * Class Contains Seller Commission Functions
 */
class Info extends Generic implements TabInterface {
    /**
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    protected $_newsStatus;
    
    /**
     *
     * @param Context $context            
     * @param Registry $registry            
     * @param FormFactory $formFactory            
     * @param Config $wysiwygConfig            
     * @param Status $newsStatus            
     * @param array $data            
     */
    public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Config $wysiwygConfig, Status $newsStatus, array $data = []) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_newsStatus = $newsStatus;
        parent::__construct ( $context, $registry, $formFactory, $data );
    }
    
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm() {
        /** @var $model \Dotsquares\Marketplace\Model\Seller */
        $model = $this->_coreRegistry->registry ( 'marketplace_seller' );
        $form = $this->_formFactory->create ();
        $fieldset = $form->addFieldset ( 'base_fieldset', [ 
                'legend' => __ ( 'Commission (%)' ) 
        ] );
        if ($model->getId ()) {
            $fieldset->addField ( 'id', 'hidden', [ 
                    'name' => 'id' 
            ] );
        }
        $fieldset->addField ( 'commission', 'text', [ 
                'name' => 'commission',
                'label' => __ ( 'Commission (%)' ),
                'required' => true,
                'class' => 'validate-zero-or-greater' 
        ] );
        $data = $model->getData ();
        $form->setValues ( $data );
        $this->setForm ( $form );
        return parent::_prepareForm ();
    }
    
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return __ ( 'Seller Commission' );
    }
    
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return __ ( 'Seller Commission' );
    }
    
    /**
     *
     * Function to show tab
     * @return boolean
     *
     */
    public function canShowTab() {
        return true;
    }
    
    /**
     *
     * Function to check hidden
     * @return boolean
     * 
     *
     */
    public function isHidden() {
        return false;
    }
}