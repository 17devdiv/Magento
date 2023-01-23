<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Dotsquares\Marketplace\Helper\Data;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\Result\RedirectFactory;

/**
 * This class blocks seller to add product in cart functions
 */
class Product implements ObserverInterface {
    protected $marketplaceData;
    protected $systemHelper;
    protected $request;
    
    /**
     *
     * @param Data $marketplaceData            
     */
    public function __construct(Data $marketplaceData, \Dotsquares\Marketplace\Helper\System $systemHelper, \Magento\Framework\Message\ManagerInterface $messagemanager, \Magento\Framework\App\Request\Http $request) {
        $this->marketplaceData = $marketplaceData;
        $this->systemHelper = $systemHelper;
        $this->messagemanager = $messagemanager;
        $this->_request = $request;
    }
    /**
     * Function to check seller product or not
     *
     * @see \Dotsquares\Marketplace\Event\ObserverInterface::execute()
     * @return url
     *
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /**
         * Get Product Details
         * 
         * @var product Id
         */
        $product = $observer->getProduct ();
        /**
         * Get seller id
         */
        $productSellerId = $product->getSellerId ();
        /**
         * Create object instance
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get logged in customer details
         */
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = '';
        /**
         * Assign customer id
         */
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getId ();
       
        /**
         * Checking for seller id and product seller id are equal or not
         */
        if ($productSellerId == $customerId) {
            /**
             * Setting session error message
             */
            $this->messagemanager->addError ( "Seller can't add their own product" );
            /**
             * Assign cart url form checkout cart helper class
             */
            $cartUrl = $objectManager->get ( 'Magento\Checkout\Helper\Cart' )->getCartUrl ();
            /**
             * Assign cart url
             */
            $url = $this->_redirect->getRedirectUrl ( $cartUrl );
            /**
             * Redirect to url
             */
            return $this->goBack ( $url );
        }
        
    }
  }
}