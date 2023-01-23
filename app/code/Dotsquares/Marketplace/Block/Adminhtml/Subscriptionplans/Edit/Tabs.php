<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Block\Adminhtml\Subscriptionplans\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class for seller subscription plans tab functions
 */
class Tabs extends WidgetTabs {
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'subscriptionplans_edit_tabs' );
        $this->setDestElementId ( 'edit_form' );
        $this->setTitle ( __ ( 'Seller Subscription Plans' ) );
    }
    
    /**
     *Function to get before html
     * @return $this
     */
    protected function _beforeToHtml() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $model = $objectManager->get ( 'Magento\Framework\Registry' )->registry ( 'marketplace_subscriptionplans' );
        $data = $model->getData ();
        if (isset ( $data )) {
            $this->addTab ( 'Subscriptionplans', [ 
                    'label' => __ ( 'Edit Subscription Plans' ),
                    'title' => __ ( 'Edit Subscription Plans' ),
                    'content' => $this->getLayout ()->createBlock ( 'Dotsquares\Marketplace\Block\Adminhtml\Subscriptionplans\Edit\Tab\Form' )->toHtml (),
                    'active' => true 
            ] );
        } else {
            $this->addTab ( 'Subscriptionplans', [ 
                    'label' => __ ( 'Add Subscription Plans' ),
                    'title' => __ ( 'Add Subscription Plans' ),
                    'content' => $this->getLayout ()->createBlock ( 'Dotsquares\Marketplace\Block\Adminhtml\Subscriptionplans\Edit\Tab\Form' )->toHtml (),
                    'active' => true 
            ] );
        }
        return parent::_beforeToHtml ();
    }
}