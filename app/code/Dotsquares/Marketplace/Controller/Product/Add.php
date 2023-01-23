<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Product;

/**
 * This class contains product add and edit functions
 */
class Add extends \Magento\Framework\App\Action\Action {
    
    /**
     * Marketplace helper data object
     *
     * @var \Dotsquares\Marketplace\Helper\Data
     */
    protected $dataHelper;
    /**
     * Constructor
     *
     * \Dotsquares\Marketplace\Helper\Data $dataHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Load add product/edit product page
     *
     * @return void
     */
    public function execute() {
        
        /**
         * Get Customer Session
         * 
         * @var unknown
         */
        
        $customerObj = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerObj->getId ();
        $seller = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $status = $seller->load ( $customerId, 'customer_id' )->getStatus ();
        
        /**
         * Checking for store name
         */
        $storeName = $seller->getStoreName ();
        if (empty ( $storeName )) {
            $this->messageManager->addNotice ( __ ( 'Kindly complete your profile details, before add New Product.' ) );
            $this->_redirect ( 'marketplace/seller/profile' );
        }
        
        if ($customerObj->isLoggedIn () && $status == 1) {
            
            /**
             * Checking for subscription enabled or not
             */
            $isSellerSubscriptionEnabled = $this->dataHelper->isSellerSubscriptionEnabled ();
            $productId = $this->getRequest ()->getParam ( 'product_id' );
            if ($isSellerSubscriptionEnabled && $productId == '') {
                /**
                 * Getting data value
                 */
                $date = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
                /**
                 * Checking for subscription profiles
                 */
                $subscriptionProfiles = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
                $subscriptionProfiles->addFieldToFilter ( 'seller_id', $customerId );
                $subscriptionProfiles->addFieldToFilter ( 'status', 1 );
                $subscriptionProfiles->addFieldtoFilter ( 'ended_at', array (
                        array (
                                'gteq' => $date 
                        ),
                        array (
                                'ended_at',
                                'null' => '' 
                        ) 
                ) );
                
                /**
                 * To count subscription profiles
                 */
                if (count ( $subscriptionProfiles )) {
                    $maxProductCount = '';
                    /**
                     * Prepare maximum product count for seller
                     */
                    foreach ( $subscriptionProfiles as $subscriptionProfile ) {
                        $maxProductCount = $subscriptionProfile->getMaxProductCount ();
                        break;
                    }
                    /**
                     * Get product collection filter by seller id
                     */
                    $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addFieldToFilter ( 'seller_id', $customerId );
                    $sellerProductIds = $product->getAllIds ();
                    /**
                     * Checking maximum product option
                     */
                    if ($maxProductCount <= count ( $sellerProductIds ) && $maxProductCount != 'unlimited') {
                        $this->messageManager->addNotice ( __ ( 'You have reached your product limit. If you want to add more product(s) kindly upgrade your subscription plan.' ) );
                        $this->_redirect ( 'marketplace/seller/subscriptionplans' );
                        return;
                    }
                } else {
                    $this->messageManager->addNotice ( __ ( 'You have not subscribed any plan yet. Kindly subscribe for adding product(s).' ) );
                    $this->_redirect ( 'marketplace/seller/subscriptionplans' );
                    return;
                }
            }
            /**
             * To load add/edit product page
             */
            $this->_view->loadLayout ();
            $this->_view->renderLayout ();
        } else {
            /**
             * Redirect to seller dashboard
             */
            $this->_redirect ( 'marketplace/seller/dashboard' );
        }
    }
}