<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml\Products\Grid\Renderer;
/**
 * This class contains product preview functions for product grid
 * @author user
 *
 */
class Productpreview extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $productId = $this->_getValue ( $row );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $storeManager = $objectManager->get ( '\Magento\Store\Model\StoreManagerInterface' );
        $productUrl = $storeManager->getStore ()->getBaseUrl ();
        $productUrl = $productUrl . 'marketplace/product/preview/id/' . $productId;
        
        return '<a  href="' . $productUrl . '" alt= "' . $productId . '" target="_blank">'.__("Preview").'</a>';
    }
}