<?php

/**
 * Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     2.0.1
 * @author      Dotsquares Team
 * @copyright   Copyright (c) 2021 Dotsquares. (https://www.dotsquares.com)
 *
 */
namespace Dotsquares\Marketplace\Controller\Product;

/**
 * This class contains product sku validation functions
 */
class Skuvalidate extends \Magento\Framework\App\Action\Action {
  
    /**
     *
     * @var $storeManager
     */
    protected $storeManager;
    /**
     * Constructor
     *
     * \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
    }
    /**
     * Function to validate product sku
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting sku from query string
         */
        $sku = trim ( $this->getRequest ()->getParam ( 'sku' ) );
       
        /**
         * Getting product collection
         */
        $productData = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addAttributeToFilter ( 'sku', $sku );
        /**
         * Getting product count
         */
        $skuCount = count ( $productData );
        /**
         * To print product count
         */
        return $skuCount;
    }
}