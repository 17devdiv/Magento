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
use Dotsquares\Marketplace\Model\ResourceModel\Order\Collection;
/**
 * This class used to display the products collection
 */
class Orders extends \Magento\Framework\View\Element\Template {
    
    /**
     * Initilize variable for product factory
     *
     * @var \Dotsquares\Marketplace\Model\ResourceModel\Order\Collection
     */
    protected $commissionObject;
    /**
     *
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $loadCurrency;
    
    /**
     *
     * @param Template\Context $templateContext            
     * @param ProductFactory $productFactory            
     *
     * @param array $data            
     */
    public function __construct(Template\Context $templateContext, Collection $commissionObject, \Magento\Framework\Locale\CurrencyInterface $loadCurrency, array $data = []) {
        $this->commissionObject = $commissionObject;
        $this->loadCurrency = $loadCurrency;
        parent::__construct ( $templateContext, $data );
    }
    public function getOrderDetails(){
        $objectManagerDashboard = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerObject = $objectManagerDashboard->get ( 'Magento\Customer\Model\Session' );
        $sellerId='';
        if ($customerObject->isLoggedIn ()) {
            $sellerId = $customerObject->getId ();
        }
        /**
         * Order collection filter by seller id
         */
        $sellerOrderCollection = $objectManagerDashboard->create ( 'Dotsquares\Marketplace\Model\ResourceModel\Order\Collection' );
        $sellerOrderCollection->addFieldToFilter ( 'seller_id', $sellerId);
        return $sellerOrderCollection;
    }
}