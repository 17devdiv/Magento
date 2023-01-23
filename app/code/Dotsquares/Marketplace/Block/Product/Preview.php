<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Resource\Product\CollectionFactory;
use Magento\CatalogInventory\Model\StockRegistry;
use Zend\Form\Annotation\Instance;

/**
 * This class used to display product preview page
 */
class Preview extends \Magento\Framework\View\Element\Template {
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     *
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection
     */
    protected $stockRegistry;
    
    /**
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, StockRegistry $stockRegistry, \Magento\Catalog\Model\Product $product, array $data = []) {
        parent::__construct ( $context, $data );
        $this->storeManager = $context->getStoreManager();
        $this->stockRegistry = $stockRegistry;
        $this->product = $product;
    }
    
    /**
     * Prepare layout for add product
     *
     * @return object
     */
    public function _prepareLayout() {
        $productId = $this->getRequest ()->getParam ( 'id' );
        $productDetails = $this->getProductData ( $productId );
        $productName = $productDetails->getName ();
        $this->pageConfig->getTitle ()->set ( $productName );
        
        return parent::_prepareLayout ();
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
     * Getting stock state object
     *
     * @param int $productId            
     *
     * @return object $stockData
     */
    public function getProductStockDataQty($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\CatalogInventory\Api\Data\StockItemInterface' )->load ( $productId, 'product_id' );
    }
    
    /**
     * Get Assign product stock using stock registry
     *
     * @param int $id            
     */
    public function getProductQty($id) {
        return $this->stockRegistry->getStockItem ( $id )->getIsInStock ();
    }
    
    /**
     * Get Product Details
     *
     * @return array
     */
    public function getPreviewProductDetatils($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $product = $objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        $sellerId = $product->getSellerId ();
        $productName = $product->getName ();
        $productSku = $product->getSku ();
        $productPrice = $product->getPrice ();
        $imagehelper = $objectManager->get ( 'Magento\Catalog\Helper\Image' );
        $productImage = $imagehelper->init ( $product, 'category_page_list' )->constrainOnly ( FALSE )->keepAspectRatio ( TRUE )->keepFrame ( FALSE )->resize ( 700 )->getUrl ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $dataHelper = $objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
        $productPrice = $dataHelper->getFormattedPrice ( $productPrice );
        $isInStock = $this->getProductQty ( $productId );
        return array (
                'sellerid' => $sellerId,
                'product_name' => $productName,
                'product_sku' => $productSku,
                'product_image' => $productImage,
                'product_price' => $productPrice,
                'isinstock' => $isInStock 
        );
    }
}
