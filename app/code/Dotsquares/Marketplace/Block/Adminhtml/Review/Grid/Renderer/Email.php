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
 * This class used to renderer email in sellers grid
 */
class Email extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $customerId = $this->_getValue ( $row );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $customerId )->getEmail ();
    }
}