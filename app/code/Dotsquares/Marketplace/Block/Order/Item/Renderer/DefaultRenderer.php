<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Order\Item\Renderer;

/**
 * Order item render block
 */
class DefaultRenderer extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer {
    
    /**
     * Get seller order details
     *
     * @param object $_item            
     * @param object $order            
     * @param object
     * @return object
     */
    public function getSellerOrderDetails($_item, $orderId, $sellerId) {
        /**
         * To get order collection by order id and seller id
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Dotsquares\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->getFirstItem ();
    }
    
    /**
     * Get seller order details
     *
     * @param object $_item            
     * @param object $order            
     * @param object
     * @return object
     */
    public function getSellerOrderItemDetails($_item, $orderId, $sellerId) {
        /**
         * Get order items collection
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Dotsquares\Marketplace\Model\Orderitems' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->getFirstItem ();
    }
    
    /**
     * Get order item action
     *
     * @return string
     */
    public function orderItemAction() {
        /**
         * Get order item url
         */
        return $this->getUrl ( 'marketplace/order/item' );
    }
}