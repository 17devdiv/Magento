<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Payments\Edit;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
/**
 * This class contains tab functions
 * @author user
 *
 */
class Tabs extends WidgetTabs {
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setDestElementId ( 'edit_form' );
        $this->setId ( 'payments_edit_tabs' );
       $this->setTitle ( __ ( 'Seller Payments' ) );
    }    
    /**
     * To prepare before to html content
     * 
     * @see \Magento\Backend\Block\Widget\Tabs::_beforeToHtml()
     * @return object
     */ 
    protected function _beforeToHtml() {
        $this->addTab ( 'Payments', [  'label' => __ ( 'Pay Seller Payments' ),'title' => __ ( 'Seller Payments' ),
                'content' => $this->getLayout ()->createBlock ( 'Dotsquares\Marketplace\Block\Adminhtml\Payments\Edit\Tab\Form' )->toHtml (),'active' => true 
        ] );
        return parent::_beforeToHtml ();
    }
}