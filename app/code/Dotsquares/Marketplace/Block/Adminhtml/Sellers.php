<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Sellers extends Container {
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct() {
        $this->_controller = 'adminhtml_sellers';
        $this->_blockGroup = 'Dotsquares_Marketplace';
        $this->_headerText = __ ( 'Manage Seller' );
        $this->_addButtonLabel = __ ( 'Add Seller' );
        $this->_addNewButton ();
        parent::_construct ();
    }
    
    /**
     * Function for New button
     * 
     * @return void
     */
    protected function _addNewButton() {
        $this->addButton ( 'add', [ 
                'label' => $this->getAddButtonLabel (),
                'onclick' => 'setLocation(\'' . $this->getCreateUrl () . '\')',
                'class' => 'add primary' 
        ] );
    }
    /**
     * Function for get Url
     * 
     * @return string
     */
    public function getCreateUrl() {
        return $this->getUrl ( 'customer/index/new' );
    }
}