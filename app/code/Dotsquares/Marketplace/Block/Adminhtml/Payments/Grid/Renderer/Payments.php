<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml\Payments\Grid\Renderer;

/**
 * This class used to renderer payment method in payments grid
 */
class Payments extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        /**
         * Get customer id by row
         */
        $customerId = $this->_getValue ( $row );
        /**
         * Create object instance
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get seller 
         */
        $sellerInfo = $objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' )->load ( $customerId, 'customer_id' );
        /**
         * Prepare html content
         */
        $html = '';
        $html = $html . '<div>';
        $html = $html . '<div>' . __ ( 'PayPal Id : ' );
        /**
         * Checking for PayPal id
         */
        if ($sellerInfo->getPaypalId () != '') {
            /**
             * Assign PayPal id
             */
            $html = $html . $sellerInfo->getPaypalId ();
        } else {
            $html = $html . __ ( 'NA' );
        }
        $html = $html . '</div>';
        $html = $html . '<div>' . __ ( 'Bank Payment : ' );
        /**
         * Checking for bank payment
         */
        if ($sellerInfo->getBankPayment () != '') {
            /**
             * Assign bank payment
             */
            $html = $html . $sellerInfo->getBankPayment ();
        } else {
            $html = $html . __ ( 'NA' );
        }
        $html = $html . '</div>';
        /**
         * Return html
         */
        return $html . '</div>';
    }
}