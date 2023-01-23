<?php

/**
 * Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     2.0.1
 * @author      Dotsquares Team
 * @copyright   Copyright (c) 2021 Dotsquares. (https://www.dotsquares.com)
 *
 */
namespace Dotsquares\Marketplace\Controller\Assignproduct;

/**
 * This class contains assign product add functions
 */
class Compare extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var $resultRawFactory
     * @var $storeManager
     */
    protected $resultRawFactory;
    protected $storeManager;
    /**
     * Constructor
     *
     * \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
     * \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Store\Model\StoreManagerInterface $storeManager) {
        parent::__construct ( $context );
        $this->resultRawFactory = $resultRawFactory;
        $this->storeManager = $storeManager;
    }
    /**
     * Function to validate product sku
     *
     * @return void
     */
    public function execute() {
        $attributeData = $this->getRequest ()->getParam ( 'attributes' );
        $currentProductId = $this->getRequest ()->getParam ( 'id' );
        $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $currentProductId );
        $productCollection = $this->_objectManager->get ( 'Magento\ConfigurableProduct\Model\Product\Type\Configurable' )->getUsedProductCollection ( $product )->addAttributeToSelect ( '*' );
        foreach ( $attributeData as $opt => $key ) {
            $productCollection->addAttributeToFilter ( $opt, $key );
        }
        $productCollectionData = $productCollection->getData ();
        foreach ( $productCollectionData as $productData ) {
            $proId [] = $productData ['entity_id'];
        }
        $proId = json_encode ( $proId );
        return $proId;
    }
}