<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Order\Seller;

/**
 * Seller order item render block
 */
class Item extends \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer {
    /**
     * Get order details
     *
     * @param int $orderId            
     * @param int $sellerId            
     *
     * @return object
     */
    public function getOrderDetails($orderId, $sellerId) {
        /**
         * To create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Return seller order details
         */
        return $objectManager->get ( 'Dotsquares\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId )->getFirstItem ();
    }
    
    /**
     * Get order item details
     *
     * @param int $orderId            
     * @param int $sellerId            
     *
     * @return object
     */
    public function getOrderItemDetails($orderId, $sellerId) {
        /**
         * Create object for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Return order item collection
         */
        return $objectManager->get ( 'Dotsquares\Marketplace\Model\Orderitems' )->getCollection ()->addFieldToFilter ( 'order_id', $orderId )->addFieldToFilter ( 'seller_id', $sellerId );
    }
    
    /**
     * Get currency symbol by code
     *
     * @param string $currencyCode            
     *
     * @return string
     */
    public function getCurrencySymbol($currencyCode) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * getting currency symbol
         */
        return $objectManager->get ( 'Dotsquares\Marketplace\Block\Order\Vieworder' )->getCurrencySymbol ( $currencyCode );
    }
    
    /**
     * Get Product Type
     *
     * @param int $productId            
     *
     * @return string
     */
    public function getProductType($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Load product details by product id
         */
        $baseConfigproduct = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        /**
         * Return product type id
         */
        return $baseConfigproduct->getTypeId ();
    }
    
    /**
     *
     * Get Attributes For Email Function
     * 
     * @return array
     */
    public function getProductAttributes($orderId) {
        /**
         * To create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Assign attribute datas
         */
        $attributeDatas = array ();
        /**
         * Get order details
         */
        $orderDetails = $objectManager->get ( 'Magento\Sales\Model\Order' );
        $orderData = $orderDetails->load ( $orderId );
        /**
         * Get all item from order data
         */
        $orderItems = $orderData->getAllItems ();
        /**
         * Iterate the order items
         */
        foreach ( $orderItems as $item ) {
            /**
             * Get product id from item
             */
            $marketplaceproductId = $item->getProductId ();
            /**
             * Get product data
             */
            $marketplaceProduct = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $marketplaceproductId );
            /**
             * Get type id
             */
            $typeId = $marketplaceProduct->getTypeId ();
            /**
             * Checking for product type equal to configurable
             */
            if ($typeId == "configurable") {
                /**
                 * Set custom options
                 */
                $customOptions = $item->getProductOptions ();
                /**
                 * Assign attribute data
                 */
                $attributeDatas [$marketplaceproductId] = $customOptions ['attributes_info'];
            }
        }
        /**
         * To return attribute datas
         */
        return $attributeDatas;
    }
}