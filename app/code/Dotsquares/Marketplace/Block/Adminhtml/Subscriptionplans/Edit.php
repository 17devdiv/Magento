<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Subscriptionplans;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class for seller subscription plans edit and Delete in manage seller subscription plans Grid
 */
class Edit extends Container {
    protected $_registryObj = null;
    /**
     * Constructor function for subscription plans grid
     * 
     * @return object
     */
    public function __construct(Context $context, Registry $registry, array $data = []) {
        $this->_registryObj = $registry;
        parent::__construct ( $context, $data );
    }
    /**
     * construct function for subscription plans
     * 
     * @return void
     */
    protected function _construct() {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_subscriptionplans';
        $this->_blockGroup = 'Dotsquares_Marketplace';
        parent::_construct ();
        $this->buttonList->update ( 'save', 'label', __ ( 'Save' ) );
        $this->buttonList->add ( 'saveandcontinue', [ 
                'label' => __ ( 'Save and Continue Edit' ),
                'class' => 'save',
                'data_attribute' => [ 
                        'mage-init' => [ 
                                'button' => [ 
                                        'event' => 'saveAndContinueEdit',
                                        'target' => '#edit_form' 
                                ] 
                        ] 
                ] 
        ], - 100 );
    }
    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout() {
        $this->_formScripts [] = "function toggleEditor() { if (tinyMCE.getInstanceById('post_content') == null) {  tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else { tinyMCE.execCommand('mceRemoveControl', false, 'post_content'); }  }; ";
        return parent::_prepareLayout ();
    }
}