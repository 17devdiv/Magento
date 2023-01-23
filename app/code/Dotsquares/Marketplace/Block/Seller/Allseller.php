<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Seller;

/**
 * This class used to display the products collection
 */
class Allseller extends \Magento\Directory\Block\Data {
    /**
     * Prepare layout for all seller
     *
     * @return object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( "All Sellers" ) );
        
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.allseller.pager' );
        $pager->setLimit ( 14 )->setShowAmounts ( false )->setCollection ( $this->getAllsellerdatas () );
        $this->setChild ( 'pager', $pager );
        
        return parent::_prepareLayout ();
    }
    
    /**
     * Function to Display All Sellers
     *
     * @params storename,logo and collection
     *
     * @return array
     *
     */
    public function getAllsellerdatas() {
        
        // get values of current page
        $page = ($this->getRequest ()->getParam ( 'p' )) ? $this->getRequest ()->getParam ( 'p' ) : 1;
        // get values of current limit
        $pageSize = ($this->getRequest ()->getParam ( 'limit' )) ? $this->getRequest ()->getParam ( 'limit' ) : 14;
        
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerModel = $objectModelManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $sellerModelCollection = $sellerModel->getCollection ();
        $sellerModelCollection->addFieldToFilter ( 'status', array (
                'eq' => 1 
        ) )->addFieldToFilter ( 'store_name', array (
                'neq' => '' 
        ) )->setPageSize ( $pageSize )->setCurPage ( $page );
        
        return $sellerModelCollection;
    }
    
    /**
     * Function to get Request Path
     *
     * @return array
     */
    public function getSellerrequestpath($sellerData) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerId = $sellerData ['customer_id'];
        $targetPath = 'marketplace/seller/displayseller/id/' . $sellerId;
        $mainUrlRewrite = $objectModelManager->get ( 'Magento\UrlRewrite\Model\UrlRewrite' )->load ( $targetPath, 'target_path' );
        $getRequestPath = $mainUrlRewrite->getRequestPath ();
        $logoName = $sellerData ['logo_name'];
        $logoImage = $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'Marketplace/Seller/Resized';
        $storeName = $sellerData ['store_name'];
        return array (
                'request_path' => $getRequestPath,
                'logo_name' => $logoName,
                'logo_path' => $logoImage,
                'store_name' => $storeName 
        );
    }
    
    /**
     * Fucntion to get All customers
     *
     * @return array
     */
    public function getAllCustomerDatas() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerModel = $objectModelManager->get ( 'Magento\Customer\Model\Customer' );
        $customerModelCollection = $customerModel->getCollection ();
        return $customerModelCollection->getData ();
    }
    
    /**
     * Function for add pagination
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
}