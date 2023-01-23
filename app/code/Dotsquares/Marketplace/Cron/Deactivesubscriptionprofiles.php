<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
namespace Dotsquares\Marketplace\Cron;

use Magento\Store\Model\ScopeInterface;

/**
 * This class contains functionality of deactive subscription profiles
 */
class Deactivesubscriptionprofiles {
    const XML_MARKETPLACE_SUBSCRIPTION_NOTIFICATION = 'marketplace/subscription/notification';
    protected $logger;
    public function __construct(\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    /**
     * Deactive subscription profiles
     */
    public function execute() {
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        
        /**
         * Get current date
         */
        $date = $objectManager->get ( 'Magento\Framework\Stdlib\DateTime\DateTime' )->gmtDate ();
        
        /**
         * Get all active subscribe profiles after date expired
         */
        $subscriptionProfilesModels = $objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
        $subscriptionProfilesModels->addFieldtoFilter ( 'ended_at', array (
                array (
                        'lteq' => $date 
                ),
                array (
                        'ended_at',
                        'null' => '' 
                ) 
        ) );
        $subscriptionProfilesModels->addFieldtoFilter ( 'status', array (
                'eq' => 1 
        ) );
        /**
         * Get profile ids
         */
        $subscriptionProfileIds = $subscriptionProfilesModels->getAllIds ();
        
        /**
         * Iterate subscription profiles by id
         */
        foreach ( $subscriptionProfileIds as $subscriptionProfileId ) {
            /**
             * Load subscription profile by id
             */
            $subscriptionProfile = $objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->load ( $subscriptionProfileId );
            /**
             * Change subscription status
             */
            $subscriptionProfile->setStatus ( 2 )->save ();
            /**
             * Assign seller id
             */
            $sellerId = $subscriptionProfile->getSellerId ();
            /**
             * Assign max product count
             */
            $maxProductCount = $subscriptionProfile->getMaxProductCount ();
            /**
             * Update seller product status by subscription
             */
            $objectManager->get ( 'Dotsquares\Marketplace\Controller\Seller\Subscriptionnotify' )->updateProductStatus ( $sellerId, 'disable', $maxProductCount );
            /**
             * Sedning for notification mail
             */
            $emailNotificationEnabled = $objectManager->get ( 'Magento\Framework\App\Config\ScopeConfigInterface' )->getValue ( static::XML_MARKETPLACE_SUBSCRIPTION_NOTIFICATION, ScopeInterface::SCOPE_STORE );
            /**
             * Checking for notification enabled or not
             */
            if ($emailNotificationEnabled == 1) {
                $this->sendNotificationMailToSeller ( $subscriptionProfile );
            }
        }
    }
    /**
     * Send notification mail to seller
     *
     * @param object $subscriptionProfile     
     * @return void       
     */
    public function sendNotificationMailToSeller($subscriptionProfile) {
        
        /**
         * Create instance for object manager
         */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        /**
         * Assign seller id
         */
        $sellerId = $subscriptionProfile->getSellerId ();
        
        /**
         * Get admin details
         */
        $admin = $objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
        $adminName = $admin->getAdminName ();
        $adminEmail = $admin->getAdminEmail ();
        
        /**
         * Get seller details
         */
        $customer = $objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $sellerId );
        
        /**
         * Assign receiver info
         */
        $receiverInfo = [ 
                'name' => $customer->getName (),
                'email' => $customer->getEmail () 
        ];
        
        /**
         * Assign sender info
         */
        $senderInfo = [ 
                'name' => $adminName,
                'email' => $adminEmail 
        ];
        
        /**
         * Declare email template
         */
        $emailTempVariables = array ();
        /**
         * Assign email template
         */
        $emailTempVariables ['sellername'] = $customer->getName ();
        $emailTempVariables ['plan'] = $subscriptionProfile->getPlanName ();
        
        /**
         * Assign template id
         */
        $templateId = 'marketplace_subscription_seller_notification_template';
        
        /**
         * Send subsciption expired notification to seller
         */
        $objectManager->get ( 'Dotsquares\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
    }
}