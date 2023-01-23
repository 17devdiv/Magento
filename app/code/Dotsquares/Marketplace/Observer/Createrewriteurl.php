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

class Createrewriteurl implements ObserverInterface {
    
    /**
     * Add rewrite url for the seller page, while create the new store view.
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /**
         * Get new store information
         */
        $newStoreInfo = $observer->getStore ();
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        
        /**
         * Get all seller information to create rewrite url for new store.
         */
        $sellerModel = $objectModelManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $sellerModelCollection = $sellerModel->getCollection ()->addFieldToSelect ( 'store_name' )->addFieldToSelect ( 'customer_id' );
        $sellerModelCollection->addFieldToFilter ( 'status', array (
                'eq' => 1 
        ) )->addFieldToFilter ( 'store_name', array (
                'neq' => '' 
        ) );
        
        /**
         * Store the targetPath in url rewrite table
         */
        foreach ( $sellerModelCollection as $storeName ) {
            $trimStr = trim ( preg_replace ( '/[^a-z0-9-]+/', '-', strtolower ( $storeName ['store_name'] ) ), '-' );
            $mainUrlRewrite = $objectModelManager->get ( 'Magento\UrlRewrite\Model\UrlRewrite' )->setStoreId ( 1 )->load ( $trimStr, 'request_path' );
            $getTargetPath = $mainUrlRewrite->getTargetPath ();
            
            if (! empty ( $getTargetPath ) && ! empty ( $trimStr )) {
                $objectModelManager->create ( 'Magento\UrlRewrite\Model\UrlRewrite' )->setIsSystem ( 0 )->setIdPath ( 'seller/' . $storeName ['store_name'] )->setTargetPath ( $getTargetPath )->setRequestPath ( $trimStr )->setStoreId ( $newStoreInfo->getStoreId () )->save ();
            }
        }
    }
}