<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Seller\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
/**
 * This class contains tab functions  in seller grid
 * @author user
 *
 */
class Tabs extends WidgetTabs {
    /**
     * Constructor for seller commission edit
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'seller_edit_tabs' );
        $this->setDestElementId ( 'edit_form' );
        $this->setTitle ( __ ( 'Commission Information' ) );
    }
    
    /**
     * To prepare before to html
     *
     * @return $this
     */
    protected function _beforeToHtml() {
        $this->addTab ( 'Commission', [ 
                'label' => __ ( 'General' ),
                'title' => __ ( 'General' ),
                'content' => $this->getLayout ()->createBlock ( 'Dotsquares\Marketplace\Block\Adminhtml\Seller\Edit\Tab\Info' )->toHtml (),
                'active' => true 
        ] );
        
        return parent::_beforeToHtml ();
    }
}