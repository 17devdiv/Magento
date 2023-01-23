<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains manipulation functions
 */
class Objectmanager extends AbstractHelper {
    
    /**
     * Get customer session
     * 
     * @return array
     */
    public function customerSession() {
        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectGroupManager->get ( 'Magento\Customer\Model\Session' );
    }
    
    /**
     * Get customer group
     *
     * @param int $customerGroupId
     * @return array
     */
    public function customerSessionGroup($customerGroupId) {
        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerGroupSession = $objectGroupManager->get ( 'Magento\Customer\Model\Group' );
        return $customerGroupSession->load ( 'Marketplace Seller', 'customer_group_code' );
    }
    
    /**
     * Get seller status
     *
     * @param int $customerGroupId
     * @return array
     */
    public function sellerCollection() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectModelManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
    }
    
    /**
    * Get seller status
    *
    * @param int $customerGroupId
    * @return array
    */
    public function sellerStatus($customerId) {
        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerGroupSession = $objectGroupManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        return $customerGroupSession->load ( $customerId, 'customer_id' )->getStatus ();
    }
    
    /**
     * Get seller status
     *
     * @param int $customerGroupId
     * @return object
     */
    public function actionController() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Magento\Framework\App\RequestInterface' );
    }
    
    /**
     * Get already assigned products
     *
     * @param int $productId
     * @return array
     */
    public function alreadyAssignedProducts($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Dotsquares\Marketplace\Block\Assignproduct\Search' )->getAlreadyAssignedProducts ( $productId );
    }
    
    /**
     * Get already assigned products
     *
     * @param int $productId
     * @return array
     */
    public function assignedProducts() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Dotsquares\Marketplace\Block\Assignproduct\Manage' );
    }
    
    /**
     * Get already assigned products
     *
     * @param int $productId
     * @return array
     */
    public function loadProduct($productId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get('Magento\Catalog\Model\Product')->load($productId);
    }
    
    /**
     * Get already assigned products
     *
     * @param int $productId
     * @return array
     */
    public function productPrice($product) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return  $objectManager->get ( 'Dotsquares\Marketplace\Block\Seller\Displayseller' )->getProductPrice ( $product );
    }
    
    /**
     * Get already assigned products
     *
     * @param int $productId
     * @return array
     */
    public function usedProductCollection($product) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return  $objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductCollection ( $product );
    }
}