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

/**
 * This class contains seller approval/disapproval functions
 */
class Seller implements ObserverInterface {
    /**
     *
     * @var $marketplaceData
     */
    protected $marketplaceData;
    
    /**
     * Constructor
     * 
     * @param Data $marketplaceData            
     */
    public function __construct(Data $marketplaceData) {
        $this->marketplaceData = $marketplaceData;
    }
    /**
     * Execute the result
     * 
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        /**
         * Get is seller param
         */
        $isSeller = $observer->getRequest ()->getPost ( 'is_seller' );
        /**
         * Checking for is seller or not
         */
        if ($isSeller) {
            /**
             * Creating instance for object manager
             */
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
            /**
             * Get customer session
             */
            $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
            /**
             * Get customer id
             */
            if ($customerSession->isLoggedIn ()) {
                /**
                 * Get customer details
                 */
                $customerId = $customerSession->getId ();
                $customerDetails = $customerSession->getCustomer ();
                $customerEmail = $customerDetails->getEmail ();
                $sellerApproval = $this->marketplaceData->getSellerApproval ();
                /**
                 * Load custome group data
                 */
                $customerGroupSession = $objectManager->get ( 'Magento\Customer\Model\Group' );
                $customerGroupData = $customerGroupSession->load ( 'Marketplace Seller', 'customer_group_code' );
                /**
                 * Get customer group id
                 */
                $sellerGroupId = $customerGroupData->getId ();
                /**
                 * Checking seller approval or not
                 */
                if ($sellerApproval) {
                    /**
                     * Set customer group id
                     */
                    $customerDetails->setGroupId ( $sellerGroupId )->save ();
                    $sellerModel = $objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
                    /**
                     * Set customer details
                     */
                    $sellerModel->setEmail ( $customerEmail )->setStatus ( 0 )->setCustomerId ( $customerId )->save ();
                } else {
                    /**
                     * Set group id to seller
                     */
                    $customerDetails->setGroupId ( $sellerGroupId )->save ();
                    /**
                     * Load seller object
                     */
                    $sellerModel = $objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
                    /**
                     * To set seller data
                     */
                    $sellerModel->setEmail ( $customerEmail )->setStatus ( 1 )->setCustomerId ( $customerId )->save ();
                }
            }
        }
    }
}