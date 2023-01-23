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
class Pay extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        /**
         * Getting customer id by row
         */
        $customerId = $this->_getValue ( $row );
        /**
         * Creating object instance
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get seller info by seller id
         */
        $sellerInfo = $objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' )->load ( $customerId, 'customer_id' );
        /**
         * Get received amount
         */
        $receivedAmount = $sellerInfo->getReceivedAmount ();
        /**
         * Get remaining amount
         */
        $remainingAmount = $sellerInfo->getRemainingAmount ();
        /**
         * Get edit url
         */
        $url = $this->getUrl ( '*/*/edit/id/' . $sellerInfo->getId () );
        $html = '';
        /**
         * Prepare html content
         */
        if ($receivedAmount == 0 && $remainingAmount == 0) {
            $html = $html . __ ( 'NA' );
        } elseif ($remainingAmount > 0) {
            $html = $html . '<a href="' . $url . '">' . __ ( 'Pay' ) . '</a>';
        } else {
            $html = $html . __ ( 'Paid' );
        }
        /**
         * Return html content
         */
        return $html;
    }
}