<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Instance;

/**
 * This class used to configurable product image
 */
class Image extends \Magento\Framework\View\Element\Template {
    
    /**
     * Get attributes based option array
     */
    public function getAttributeBasedOptions($options) {
        $optionsArray = array ();
        $attributeCodes = array ();
        foreach ( $options as $optionId => $attributeCode ) {
            $optionsArray [$attributeCode] [] = $optionId;
            if (! in_array ( $attributeCode, $attributeCodes )) {
                $attributeCodes [] = $attributeCode;
            }
        }
        return array (
                'options' => $optionsArray,
                'attribute_code' => $attributeCodes 
        );
    }
    
    /**
     * Get attribute details
     *
     * @return string
     */
    public function getAttributeData($attributeCode) {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product\Attribute\Repository' )->get ( $attributeCode );
    }
    
    /**
     * Get media image url
     *
     * @return string
     */
    public function getMediaImageUrl() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseUrl ( \Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . 'catalog/product';
    }
    /**
     * Getting product data
     *
     * @param int $productId            
     *
     * @return object $productData
     */
    public function getProductData($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
    }
    
    /**
     * Get ajax image upload action url
     *
     * @return string
     */
    public function getConfigurableImageUploadAjaxUrl() {
        return $this->getUrl ( 'marketplace/product/imageupload' );
    }
    
    /**
     * Get ajax summary ajax url
     *
     * @return string
     */
    public function getConfigurableSummaryAjaxUrl() {
        return $this->getUrl ( 'marketplace/configurable/summary' );
    }
    
    /**
     * Price option in configurable product bulk section
     *
     * @return string
     */
    public function getPriceBaseCurrency() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Dotsquares\Marketplace\Block\Product\Add' )->getBaseCurrency ();
    }
    
    /**
     * Get video url
     *
     * @return string
     */
    public function getYoutubeUrl(){
        return $this->getUrl ( 'marketplace/product/videoupload' );
    }
    
    /**
     * Get store config option value
     *
     * @return int
     */
    public function getVideoEnabled() {
        return $this->_scopeConfig->getValue ( 'marketplace/product/product_video', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Get store config option value
     *
     * @return int
     */
    public function getApiKey() {
        return $this->_scopeConfig->getValue ( 'catalog/product_video/youtube_api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Get product details
     * 
     * @param int $productId
     *
     * @return array
     */
    public function getProductDetails($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
    }
}
