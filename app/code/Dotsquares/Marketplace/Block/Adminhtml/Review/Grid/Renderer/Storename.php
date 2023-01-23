<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml\Review\Grid\Renderer;
/**
 * This class contains store name functions for review grid
 * @author user
 *
 */
class Storename extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $storeName = '';
        $sellerId = $this->_getValue ( $row );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $storeName = $objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' )->load ( $sellerId, 'customer_id' )->getStoreName ();
        if (empty ( $storeName )) {
            $storeName = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $sellerId )->getFirstname ();
        }
        return $storeName;
    }
}