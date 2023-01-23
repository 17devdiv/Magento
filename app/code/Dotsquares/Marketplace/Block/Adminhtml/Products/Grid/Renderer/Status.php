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
 * This class contains status functions for product grid
 * @author user
 *
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $productApprovalStatus = $this->_getValue ( $row );
        if ($productApprovalStatus == 1) {
            $status = "Approved";
        } else {
            $status = "Disapproved";
        }
        
        return $status;
    }
}