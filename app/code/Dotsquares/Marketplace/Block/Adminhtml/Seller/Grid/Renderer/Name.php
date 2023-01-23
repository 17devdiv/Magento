<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml\Seller\Grid\Renderer;
/**
 * This class contains rendered functions for name in seller grid
 * @author user
 *
 */
class Name extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $name = '';
        $customerId = $this->_getValue ( $row );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerDetails = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $customerId );
        $name = $customerDetails->getFirstname ();
        $sellerUrl = $this->getUrl ( 'customer/index/edit/id/' . $customerId );
        return '<a  href="' . $sellerUrl . '" alt= "' . $name . '">' . $name . '</a>';
    }
}