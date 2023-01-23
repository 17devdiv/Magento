<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;

class CheckLogin implements ObserverInterface
{
    protected $messageManager;
    protected $_responseFactory;
    protected $_url;
    protected $redirect;
    protected $catalogSession;
    public function __construct(Session $session, \Magento\Framework\App\ResponseFactory $responseFactory, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\UrlInterface $url, \Magento\Framework\App\Response\RedirectInterface $redirect, \Magento\Catalog\Model\Session $catalogSession) {
        $this->_session = $session;
        $this->messageManager = $messageManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->redirect = $redirect;
        $this->catalogSession = $catalogSession;
    }
    
    public function execute(Observer $observer)
    {
        $items = $this->_session->getQuote()->getAllItems();
        foreach ( $items as $item ) {
            $productId = $item->getProductId ();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollectionFactory = $objectManager->get ( '\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory' );
            $productCollectionFactory = $productCollectionFactory->create ();
            $productCollectionFactory->addAttributeToSelect ( '*' );
            $productCollectionFactory->addFieldToFilter ( 'entity_id', $productId );
            foreach ( $productCollectionFactory as $product ) {
                $productSellerId = $product->getSellerId ();
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
                        $this->catalogSession->setMyValue__("Seller can't add their own product");
                        $this->_responseFactory->create()->setRedirect('/');
                        return false;
                    }
                }
            }
        }
    }
}