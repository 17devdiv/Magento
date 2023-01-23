<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Order\Item;

/**
 * Order item render block
 */
class Request extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer {
    
    /**
     * Get order details
     *
     * @param int $orderId            
     * @param int $sellerId            
     *
     * @return object $sellerOrder
     */
    public function getOrderDetails($orderId, $sellerId) {
        /**
         * To create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * To get order details
         */
        return $objectManager->get ( 'Dotsquares\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->getFirstItem ();
    }
    
    /**
     * Get order item details
     *
     * @param int $orderId            
     * @param int $sellerId            
     * @param int $productId            
     *
     * @return object $sellerOrderItems
     */
    public function getOrderItemDetails($orderId, $sellerId, $productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * To get order item details
         */
        return $objectManager->get ( 'Dotsquares\Marketplace\Model\Orderitems' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'product_id', $productId )->getFirstItem ();
    }
    
    /**
     * Get currency symbol by code
     *
     * @param string $currencyCode            
     *
     * @return string
     */
    public function getCurrencySymbol($currencyCode) {
        /**
         * Create object
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get get order currency symbol
         */
        return $objectManager->get ( 'Dotsquares\Marketplace\Block\Order\Vieworder' )->getCurrencySymbol ( $currencyCode );
    }
}