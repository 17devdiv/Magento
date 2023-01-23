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
class Sellerproducts extends \Magento\Directory\Block\Data {
    /**
     * Prepare display seller layout
     *
     * @return Object
     */
    public function _prepareLayout() {
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.allproducts.pager' );
        $pager->setLimit ( 12 )->setShowAmounts ( false )->setCollection ( $this->getAllProducts () );
        $this->setChild ( 'pager', $pager );
        return parent::_prepareLayout ();
    }
    /**
     * display seller construct
     *
     * @return void
     */
    public function getAllProducts() {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerId = $this->getRequest ()->getParam ( 'id' );
        
        // get values of current page
        $page = ($this->getRequest ()->getParam ( 'p' )) ? $this->getRequest ()->getParam ( 'p' ) : 1;
        // get values of current limit
        $pageSize = ($this->getRequest ()->getParam ( 'limit' )) ? $this->getRequest ()->getParam ( 'limit' ) : 12;
        
        return $objectModelManager->get ( 'Magento\Catalog\Model\ResourceModel\Product\Collection' )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $customerId )->addAttributeToFilter ( 'product_approval', 1 )->addAttributeToFilter ( 'status', 1 )->addAttributeToFilter ( 'visibility', array (
                'eq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH 
        ) )->setPageSize ( $pageSize )->setCurPage ( $page );
    }
    
    /**
     * Function for add pagination
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product            
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product) {
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $url = $objectModelManager->get ( 'Magento\Checkout\Helper\Cart' )->getAddUrl ( $product );
        return [ 
                'action' => $url,
                'data' => [ 
                        'product' => $product->getEntityId (),
                        \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $objectModelManager->get ( 'Magento\Framework\Url\Helper\Data' )->getEncodedUrl ( $url ) 
                ]
                 
        ];
    }
    
    
}