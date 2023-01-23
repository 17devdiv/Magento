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
 * This class contains seller email functions for product grid
 * @author user
 *
 */
class SellerEmail extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $sellerId = $this->_getValue ( $row );
        $email = '';
        if ($sellerId != '') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $sellerDetails = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $sellerId );
            $email = $sellerDetails->getEmail ();
        }
        return $email;
    }
}