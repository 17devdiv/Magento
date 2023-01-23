<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Review\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
/**
 * This class contains tab functions for review edit feature
 * @author user
 *
 */
class Tabs extends WidgetTabs {
    /**
     * Construct class for seeting review edit tabs
     *  
     * @see \Magento\Framework\View\Element\Template::_construct()
     * @return void
     * 
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'review_edit_tabs' );
        $this->setDestElementId ( 'edit_form' );
        $this->setTitle ( __ ( 'Seller Review' ) );
    }
    /**
     * Get before html content
     * 
     * @see \Magento\Backend\Block\Widget\Tabs::_beforeToHtml()
     * @return object
     */
    protected function _beforeToHtml() {
        /**
         * Add Review button
         */
        $this->addTab ( 'Review', ['label' => __ ( 'Edit Review' ),'title' => __ ( 'Edit Review' ),
                'content' => $this->getLayout ()->createBlock ( 'Dotsquares\Marketplace\Block\Adminhtml\Review\Edit\Tab\Info' )->toHtml (),
                'active' => true] );
        return parent::_beforeToHtml ();
    }
}