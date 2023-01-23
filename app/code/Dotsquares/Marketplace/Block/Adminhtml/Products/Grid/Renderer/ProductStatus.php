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
 * This class contains product status functions for product grid
 * @author user
 *
 */
class ProductStatus extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $productStatus = $this->_getValue ( $row );
        if ($productStatus == 1) {
            $status = "Enabled";
        } else {
            $status = "Disabled";
        }
        
        return $status;
    }
}