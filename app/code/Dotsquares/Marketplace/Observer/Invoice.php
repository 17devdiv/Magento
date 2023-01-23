<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * This class contains order refund functions
 */
class Invoice implements ObserverInterface {
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /**
         * Get Order Details
         * 
         * @var unknown
         */
        $invoice = $observer->getEvent ()->getInvoice ();
        $order = $invoice->getOrder ();
        $sellerNotInvoice = $allSellerId = array ();
        foreach ( $invoice->getAllItems () as $item ) {
            
            if ($item->getOrderItem ()->getParentItem ()) {
                continue;
            }
            
            /**
             * Get Product Data
             * 
             * @var int(Product Id)
             */
            $productId = $item->getProductId ();
            /**
             * Create object instance
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            /**
             * Load product data by product id
             */
            $product = $objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId );
            /**
             * Assign seller id
             */
            $sellerId = $product->getSellerId ();
            
            /**
             * Checking for seller id exist or not
             */
            if (! empty ( $sellerId )) {
                if ($item->getOrderItem ()->getQtyOrdered () != $item->getQty ()) {
                    $sellerNotInvoice [] = $sellerId;
                }
                $allSellerId [] = $sellerId;
            }
        }
        
        /**
         * Get all seller id for invoice
         */
        if (count ( $allSellerId ) >= 1) {
            $allSellerId = array_unique ( $allSellerId );
        }
        
        if (count ( $sellerNotInvoice ) >= 1) {
            $sellerNotInvoice = array_unique ( $sellerNotInvoice );
            $allSellerId = array_diff ( $allSellerId, $sellerNotInvoice );
        }
        
        foreach ( $allSellerId as $allSeller ) {
            /**
             * Update seller order status
             */
            $sellerOrderCollection = $objectManager->get ( 'Dotsquares\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $order->getId () )->addFieldToFilter ( 'seller_id', $allSeller )->getFirstItem ();
            
            $totalAmount = $sellerOrderCollection->getSellerAmount () + $sellerOrderCollection->getShippingAmount ();
            $this->updateSellerAmount ( $allSeller, $totalAmount );
        }
        $invoice=$order->canShip();
        if($invoice){
            $orderStatus='processing';
        }
        else{
            $orderStatus='completed';
        }
        $sellerOrderCollection = $objectManager->get ( 'Dotsquares\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $order->getId () );
        $sellerOrderCollectionDatas =$sellerOrderCollection->getData();
        foreach($sellerOrderCollectionDatas as $sellerOrderCollectionData){
            $objectManager->get ( 'Dotsquares\Marketplace\Model\Order' )->load($sellerOrderCollectionData['id'])->setStatus($orderStatus)->save();
        }
        
    }
    
    /**
     * Update seller amount
     *
     * @param int $updateSellerId            
     * @param double $totalAmount            
     *
     * @return void
     */
    public function updateSellerAmount($updateSellerId, $totalAmount) {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Load seller by seller id
         */
        $sellerModel = $objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $sellerDetails = $sellerModel->load ( $updateSellerId, 'customer_id' );
        /**
         * Get remaining amount
         */
        $remainingAmount = $sellerDetails->getRemainingAmount ();
        /**
         * Total remaining amount
         */
        $totalRemainingAmount = $remainingAmount + $totalAmount;
        /**
         * Set total remaining amount
         */
        $sellerDetails->setRemainingAmount ( $totalRemainingAmount );
        /**
         * Save remaining amount
         */
        $sellerDetails->save ();
    }
}