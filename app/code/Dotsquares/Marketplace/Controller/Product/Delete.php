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
 * This class contains Product Delete Functions
 */
class Delete extends \Magento\Framework\App\Action\Action {
    /**
     * Marketplace helper data object
     *
     * @var object $dataHelper
     * @var object $messageManager
     */
    protected $dataHelper;
    protected $messageManager;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Dotsquares\Marketplace\Helper\Data $dataHelper            
     * @param \Magento\Framework\Message\ManagerInterface $messageManager            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        $this->messageManager = $context->getMessageManager();
        parent::__construct ( $context );
    }
    /**
     * Load product delete product page
     *
     * @return void
     */
    public function execute() {
        
        /**
         * Setting delete flag
         */
        $deleteFlag = 0;
        /**
         * Getting product id from query string
         */
        $productId = $this->getRequest ()->getParam ( 'product_id' );
        /**
         * Getting delete product flag
         */
        $deleteFlag = $this->sellerProductDelete ( $productId );
        /**
         * Checking whether delete flag value equal to one or not
         */
        if ($deleteFlag == 1) {
            /**
             * Setting success session message
             */
            $this->messageManager->addSuccess ( __ ( 'The product has been deleted successfully.' ) );
        } else {
            /**
             * Setting error session message
             */
            $this->messageManager->addError ( __ ( 'You dont have access to delete this product.' ) );
        }
        /**
         * Redirect to manage page
         */
        $this->_redirect ( '*/*/manage' );
    }
    /**
     * Function to delete seller products
     *
     * @return boolean
     */
    public function sellerProductDelete($productId) {
        /**
         * Setting delete flag
         */
        $deleteFlag = 0;
       
        /**
         * Getting logged in customer object
         */
        $customer = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $customer->getId ();
        /**
         * Get product object
         */
        $productObject = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $productId );
        $productObjectSellerId = $productObject->getSellerId ();
        /**
         * Checking whether seller id is equal to product seller id
         */
        if ($sellerId == $productObjectSellerId) {
            /**
             * To set register for secure area
             */
            $this->_objectManager->get ( 'Magento\Framework\Registry' )->register ( 'isSecureArea', true );
            /**
             * To delete product
             */
            $productObject->delete ();
            /**
             * To unregister for secure area
             */
            $this->_objectManager->get ( 'Magento\Framework\Registry' )->unregister ( 'isSecureArea' );
            /**
             * Set delete flag
             */
            $deleteFlag = 1;
        }
        /**
         * To return delete flag
         */
        return $deleteFlag;
    }
}