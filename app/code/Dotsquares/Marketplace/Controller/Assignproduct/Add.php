<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Assignproduct;

/**
 * This class contains assign product add functions
 */
class Add extends \Magento\Framework\App\Action\Action {
    /**
     * Data Helper
     *
     * @var unknown
     */
    protected $dataHelper;
    /**
     * Constructo Function
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Dotsquares\Marketplace\Helper\Data $dataHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    /**
     * Function to load add assign product layout
     *
     * @return $array
     */
    public function execute() {
        $this->checkSubscriptionPlan ();
        $this->_objectManager->get ( 'Dotsquares\Marketplace\Controller\Assignproduct\Manage' )->checkSellerEnabledorNot ();
    }
    /**
     * Checking for product limit based on subscription
     */
    public function checkSubscriptionPlan() {
        /**
         * Checking for subscription enabled or not
         */
        
        $loggedInUser = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $sellerId = $loggedInUser->getId ();
        $checkSubscriptionPlans = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' )->isSellerSubscriptionEnabled ();
        if ($checkSubscriptionPlans) {
            $currentDate = $this->_objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
            /**
             * To count subscription profiles
             */
            $subscribedData = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
            $subscribedData->addFieldToFilter ( 'seller_id', $sellerId );
            $subscribedData->addFieldToFilter ( 'status', 1 );
            $subscribedData->addFieldtoFilter ( 'ended_at', array (array ('gteq' => $currentDate),
                    array ('ended_at','null' => '')));
            /**
             * Prepare maximum product limt for seller
             */
            if (count ( $subscribedData )) {
                $productLimit = '';
                foreach ( $subscribedData as $subscribeInfo ) {
                    $productLimit = $subscribeInfo->getMaxProductCount ();
                    break;
                }
                $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId );
                $sellerProductIds = $product->getAllIds ();
                /**
                 * Checking maximum product limit
                 */
                if ($productLimit <= count ( $sellerProductIds ) && $productLimit != 'unlimited') {
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
    }

}