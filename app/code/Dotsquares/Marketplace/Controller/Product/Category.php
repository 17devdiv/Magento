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

use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * This class contains loading category functions
 */
class Category extends \Magento\Framework\App\Action\Action {
    
    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     *
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    protected $dataHelper;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager            
     * @param CategoryRepositoryInterface $categoryRepository            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, CategoryRepositoryInterface $categoryRepository, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        parent::__construct ( $context );
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->dataHelper = $dataHelper;
    }
    
    /**
     * Execute the result
     *
     * @return $resultPage
     */
    public function execute() {
        
        /**
         * Get values from post data from ajax request for showing the sub level categories
         */
        $categoryId = trim ( $this->getRequest ()->getParam ( 'category_id' ) );
        $category = $this->categoryRepository->get ( $categoryId, $this->storeManager->getStore ()->getId () );
        $subcategories = $category->getChildrenCategories ();
        foreach ( $subcategories as $category ) {
            $catId = $category->getId ();
            /**
             * Condition to check for sub category
             */
            if ($category->hasChildren ()) {
                $catId = $category->getId () . 'sub';
            }
            $customerName [$catId] = $category->getName ();
        }
        /**
         * Sort in alphabatical order.
         */
        asort ( $customerName );
        
        /**
         * The decode selected category ids
         */
        $catChecked = json_decode ( trim ( $this->getRequest ()->getPost ( 'selectedCatIds' ) ) );
        
        /**
         * Show categories tree
         */
        return $this->dataHelper->showCategoriesTree ( $customerName, $catChecked );
    }
}
