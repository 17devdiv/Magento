<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Observer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\ObserverInterface;

/**
 * Customer Observer Model
 */
class AfterAddressSaves implements ObserverInterface
{

    /**
     * @var CustomerSession
     */
    private $customerSession;
protected $_customerRepositoryInterface;

    /**
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerSession $customerSession,\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->customerSession = $customerSession;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }
    /**
     * Address after save event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
        $customerEmail = $customerSession->getCustomer()->getEmail();
        $customerId = $customerSession->getCustomer()->getId();
        $customer = $this->_customerRepositoryInterface->getById($customerId);
        $customerEmail = $customer->getEmail();
        $sellerModels = $objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $load = $sellerModels->load ($customerId, 'customer_id' );
        $sellerEmail = $load->getEmail ();
        if($customerEmail != $sellerEmail){
        $load->setEmail ( $customerEmail)->save ();
        }
      }
}
}