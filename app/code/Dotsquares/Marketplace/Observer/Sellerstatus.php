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
use Magento\Catalog\Model\ResourceModel\Product\Action;

/**
 * This class contains seller approval/disapproval functions
 */
class Sellerstatus implements ObserverInterface {
    protected $action;
    public function __construct(Action $action) {
        $this->action = $action;
    }
    
    /**
     * Execute the result
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $product = $observer->getProduct ();
        $productSellerId = $product->getSellerId ();
        if ($productSellerId) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $sellerModel = $objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' )->load ( $productSellerId, 'customer_id' );
            $sellerStatus = $sellerModel->getStatus ();
            if ($sellerStatus == '0') {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
                $storeManager = $objectManager->get ( '\Magento\Store\Model\StoreManagerInterface' );
                $storeId = $storeManager->getStore ()->getStoreId ();
                $this->action->updateAttributes ( [ 
                        $product->getEntityId () 
                ], [ 
                        'status' => 2 
                ], $storeId );
            }
        }
    }
}
