<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Seller;

use Magento\Framework\View\Element\Template;

/**
 * This class used to display the products collection
 */
class Transactions extends \Magento\Framework\View\Element\Template {
    
    /**
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $localeCurrency;
    
    /**
     *
     * Manage order block construct
     *
     * @param Template\Context $context            
     * @param ProductFactory $productFactory            
     * @param array $data            
     *
     * @return void
     */
    public function __construct(Template\Context $context, \Magento\Framework\Locale\CurrencyInterface $localeCurrency, array $data = []) {
        $this->localeCurrency = $localeCurrency;
        parent::__construct ( $context, $data );
    }
    
    /**
     * Set product collection uisng ProductFactory object
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        /**
         * Creating object for customer session
         */
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectModelManager->get ( 'Magento\Customer\Model\Session' );
        /**
         * Declare customer id
         */
        $customerId='';
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getId ();
        }
        
        /**
         * Filter by selle id
         */
        $collection = $objectModelManager->get ( 'Dotsquares\Marketplace\Model\Payments' )->getCollection ()->addFieldToSelect ( '*' );
        $collection->addFieldToFilter ( 'seller_id', $customerId );
        
        /**
         * Set order for manage order
         */
        $collection->setOrder ( 'id', 'desc' );
        $this->setCollection ( $collection );
    }
    
    /**
     * Prepare layout for view seller order
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        /**
         * Setting title for manage order
         */
        $this->pageConfig->getTitle ()->set ( __ ( "Transactions" ) );
        /**
         * Call perant prepare layout
         */
        parent::_prepareLayout ();
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.transaction.manage.pager' );
        /**
         * Setting limit
         */
        $pager->setLimit ( 10 )->setShowAmounts ( false )->setCollection ( $this->getCollection () );
        /**
         * Setting child
         */
        $this->setChild ( 'pager', $pager );
        /**
         * Load collection
         */
        $this->getCollection ()->load ();
        /**
         * Return layout
         */
        return $this;
    }
    
    /**
     * Get currency symbol by code
     *
     * @param string $currencyCode            
     *
     * @return string
     */
    public function getCurrencySymbol() {
        /**
         * To get currency symbol
         */
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $currencyCode = $objectModelManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ()->getBaseCurrencyCode ();
        if ($this->localeCurrency->getCurrency ( $currencyCode )->getSymbol ()) {
            $currencyCode = $this->localeCurrency->getCurrency ( $currencyCode )->getSymbol ();
        }
        return $currencyCode;
    }
    
    /**
     * Prepare Page Html
     *
     * @return string
     */
    public function getPagerHtml() {
        /**
         * To get child html
         */
        return $this->getChildHtml ( 'pager' );
    }
}