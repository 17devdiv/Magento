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
 * This class contains product link functions for product grid
 * @author user
 *
 */
class ProductLink extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $productId = $this->_getValue ( $row );
        $productUrl = $this->getUrl ( 'catalog/product/view/id/' . $productId );
        return '<a  href="' . $productUrl . '" alt= "' . $productId . '"/>';
    }
}