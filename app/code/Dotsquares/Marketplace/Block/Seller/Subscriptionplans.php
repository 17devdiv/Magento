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
use Magento\Store\Model\ScopeInterface;

/**
 * This class used to display the seller subscription plans
 */
class Subscriptionplans extends \Magento\Framework\View\Element\Template {
    const XML_SUBSCRIPTION_MERCHANT_PAYPAL_ID = 'marketplace/subscription/merchant_paypal_id';
    const XML_SUBSCRIPTION_SANDBOX_MODE = 'marketplace/subscription/sandbox_mode';
    
    public function __construct(Template\Context $context, array $data = []) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $context->getStoreManager();
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
         * To create instance for object manager
         */
        $objectModelManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get subscription plan collection
         */
        $subscriptionPlans = $objectModelManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionplans' )->getCollection ();
        /**
         * Filter by status
         */
        $subscriptionPlans->addFieldToFilter ( 'status', 1 );
        /**
         * Order by id
         */
        $subscriptionPlans->setOrder ( 'id', 'desc' );
        /**
         * Set order for seller subscription
         */
        $this->setCollection ( $subscriptionPlans );
    }
    
    /**
     * Prepare layout for view seller order
     *
     * @return object $this
     */
    protected function _prepareLayout() {
        $planId = $this->getRequest ()->getParam ( 'plan_id' );
        
        if (empty ( $planId )) {
            $this->pageConfig->getTitle ()->set ( __ ( "Subscription Plans" ) );
        } else {
            $this->pageConfig->getTitle ()->set ( __ ( "" ) );
        }
        parent::_prepareLayout ();
        /**
         *
         * @var \Magento\Theme\Block\Html\Pager
         */
        $html = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'marketplace.subscription.plans.manage.pager' );
        $html->setLimit ( 10 )->setShowAmounts ( false )->setCollection ( $this->getCollection () );
        $this->setChild ( 'pager', $html );
        $this->getCollection ()->load ();
        return $this;
    }
    
    /**
     * Get seller subscription url
     *
     * @return string
     */
    public function getSellerSubscriptionUrl() {
        return $this->getUrl ( 'marketplace/seller/paypal' );
    }
    
    /**
     * Get Merchant PayPal Id
     *
     * @return string
     */
    public function getMerchantPayPalId() {
        return $this->scopeConfig->getValue ( static::XML_SUBSCRIPTION_MERCHANT_PAYPAL_ID, ScopeInterface::SCOPE_STORE );
    }
    
    /**
     * Prepare Page Html
     *
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Get sandbox enabled or not
     *
     * @return string $actionUrl
     */
    public function getActionUrl() {
        $isSandboxEnabled = $this->scopeConfig->getValue ( static::XML_SUBSCRIPTION_SANDBOX_MODE, ScopeInterface::SCOPE_STORE );
        /**
         * Get paypal action url
         */
        if ($isSandboxEnabled == 1) {
            $actionUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $actionUrl = 'https://www.paypal.com/cgi-bin/webscr';
        }
        return $actionUrl;
    }
    
    /**
     * Get plan fee
     *
     * @param int $planId            
     *
     * @return object
     */
    public function getSubscriptionPlansData($planId) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        return $objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionplans' )->load ( $planId );
    }
    
    /**
     * Get Base Currency Code
     *
     * @return string
     */
    public function getBaseCurrencyCode() {
        return $this->storeManager->getStore ()->getBaseCurrencyCode ();
    }
    
    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
        return $customerSession->getId ();
    }
    
    /**
     * Save subscription profile data
     *
     * @param int $planId            
     *
     * @return void
     */
    public function saveSubscriptionProfileData($planId, $invoiceId, $baseCurrencyCode) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Get subscription plan object
         */
        $subscriptionPlansModel = $objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionplans' );
        $subscriptionPlansModel->load ( $planId );
        
        if ($subscriptionPlansModel->getId ()) {
            $date = $objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
            $customerSession = $objectManager->get ( 'Magento\Customer\Model\Session' );
            $sellerId = $customerSession->getId ();
            $receiverEmailId = $this->getMerchantPayPalId ();
            
            $subscriptionProfilesModel = $objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' );
            /**
             * Saving seller subscription plan
             */
            $subscriptionProfilesModel->setPlanId ( $subscriptionPlansModel->getId () );
            $subscriptionProfilesModel->setPlanName ( $subscriptionPlansModel->getPlanName () );
            $subscriptionProfilesModel->setSubscriptionPeriodType ( $subscriptionPlansModel->getSubscriptionPeriodType () );
            $subscriptionProfilesModel->setPeriodFrequency ( $subscriptionPlansModel->getPeriodFrequency () );
            $subscriptionProfilesModel->setMaxProductCount ( $subscriptionPlansModel->getMaxProductCount () );
            $subscriptionProfilesModel->setFee ( $subscriptionPlansModel->getFee () );
            
            /**
             * Store seller details
             */
            $subscriptionProfilesModel->setSellerId ( $sellerId );
            $subscriptionProfilesModel->setCreatedAt ( $date );
            $subscriptionProfilesModel->setStartedAt ( $date );
            $subscriptionProfilesModel->setInvoice ( $invoiceId );
            $subscriptionProfilesModel->setReceiverEmail ( $receiverEmailId );
            $subscriptionProfilesModel->setBaseCurrencyCode ( $baseCurrencyCode );
            
            /**
             * Calculate end date
             */
            if ($subscriptionPlansModel->getPeriodFrequency () > 1) {
                $endTimestamp = strtotime ( "+" . $subscriptionPlansModel->getPeriodFrequency () . " " . $subscriptionPlansModel->getSubscriptionPeriodType (), strtotime ( $date ) );
            } else {
                $endTimestamp = strtotime ( "+" . $subscriptionPlansModel->getPeriodFrequency () . " " . $subscriptionPlansModel->getSubscriptionPeriodType () . "s", strtotime ( $date ) );
            }
            
            $subscriptionProfilesModel->setEndedAt ( $endTimestamp );
            $subscriptionProfilesModel->setStatus ( 0 );
            $subscriptionProfilesModel->save ();
        }
    }
    
    /**
     * Get subscription profile data
     *
     * @return object
     */
    public function getSubscriptionProfileData() {
        $subscriptionProfileValue = array ();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $date = $objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
        $customerObj = $objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerObj->getId ();
        $subscriptionProfiles = $objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
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
        if (count ( $subscriptionProfiles )) {
            foreach ( $subscriptionProfiles as $subscriptionProfile ) {
                $subscriptionProfileValue = $subscriptionProfile;
                break;
            }
        }
        return $subscriptionProfileValue;
    }
}