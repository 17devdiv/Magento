<?php

/**
 * Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     2.0.1
 * @author      Dotsquares Team
 * @copyright   Copyright (c) 2021 Dotsquares. (https://www.dotsquares.com)
 *
 */
namespace Dotsquares\Marketplace\Controller\Seller;

/**
 * This class contains load seller store functions
 */
class Subscriptionnotify extends \Magento\Framework\App\Action\Action {
    /**
     * Marketplace helper data object
     *
     * @var \Dotsquares\Marketplace\Helper\Data
     */
    protected $dataHelper;
    /**
     * Constructor
     *
     * \Dotsquares\Marketplace\Helper\Data $dataHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load seller store page
     *
     * @return $array
     */
    public function execute() {
        $rawPostData = file_get_contents ( 'php://input' );
        $rawPostArray = explode ( '&', $rawPostData );
        $myPost = array ();
        foreach ( $rawPostArray as $keyval ) {
            $keyval = explode ( '=', $keyval );
            if (count ( $keyval ) == 2){
                $myPost [$keyval [0]] = urldecode ( $keyval [1] );
            }
        }
        
        $req = 'cmd=_notify-validate';
        if (function_exists ( 'get_magic_quotes_gpc' )) {
            $get_magic_quotes_exists = true;
        }
        foreach ( $myPost as $key => $value ) {
            $true = true;
            if ($get_magic_quotes_exists == $true) {
                $value = urlencode ( stripslashes ( $value ) );
            } else {
                $value = urlencode ( $value );
            }
            $req .= "&$key=$value";
        }
        /**
         * Get sandbox mode.
         */
        
       $actionUrl = $this->_objectManager->get ( 'Dotsquares\Marketplace\Block\Seller\Subscriptionplans' )->getActionUrl ();
        
        /**
         * Curl function to get response from paypal.
         */
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $actionUrl );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $req );
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        
        /**
         * inspect IPN validation result and act accordingly
         */
        if (strcmp ( $result, "VERIFIED" ) == 0) {
            /**
             * Get parameter value.
             */
            $paymentStatus = $this->getRequest ()->getPost ( 'payment_status' );
            $txnId = $this->getRequest ()->getPost ( 'txn_id' );
            $paymentDate = $this->getRequest ()->getPost ( 'payment_date' );
            $itemName = $this->getRequest ()->getPost ( 'item_name' );
            $invoice = $this->getRequest ()->getPost ( 'invoice' );
            $settleAmount = $this->getRequest ()->getPost ( 'mc_gross' );
       
            /**
             * Get subscription profiles object
             */
            $subscriptionProfilesModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->load ( $invoice, 'invoice' );
            $sellerId = $subscriptionProfilesModel->getSellerId ();
            
            /**
             * Disable all subscription plans
             */
            $subscriptionProfilesModels = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->getCollection ();
            $subscriptionProfilesModels->addFieldtoFilter ( 'status', array (
                    'eq' => 1 
            ) );
            $subscriptionProfilesModels->addFieldtoFilter ( 'id', array (
                    'neq' => $subscriptionProfilesModel->getId () 
            ) );
            $subscriptionProfilesModels->addFieldtoFilter ( 'seller_id', array (
                    'eq' => $sellerId 
            ) );
            $subscriptionProfileIds = $subscriptionProfilesModels->getAllIds ();
            foreach ( $subscriptionProfileIds as $subscriptionProfileId ) {
                $subscriptionProfile = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->load ( $subscriptionProfileId );
                $subscriptionProfile->setStatus ( 2 )->save ();
            }
            
            /**
             * Get subscription profiles object
             */
            $subscriptionProfilesModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionprofiles' )->load ( $invoice, 'invoice' );
            if ($subscriptionProfilesModel->getTxnId () == '') {
                $subscriptionProfilesModel->setStatus ( 1 );
                $subscriptionProfilesModel->setTxnId ( $txnId );
                $subscriptionProfilesModel->setCreatedAt ( $paymentDate );
                $subscriptionProfilesModel->setFee ( $settleAmount );
                $subscriptionProfilesModel->setPaymentStatus ( $paymentStatus );
                $subscriptionProfilesModel->setItemName ( $itemName );
                $subscriptionProfilesModel->save ();
            }
        }
    }
    
    /**
     * Update product status
     *
     * @param int $sellerId            
     * @param string $action            
     * @param string $maxProductCount            
     *
     * @return void
     *
     */
    public function updateProductStatus($sellerId, $action, $maxProductCount) {
        /**
         * To create object for getting product collection by seller id
         */
        $product = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId );
        $sellerProductIds = $product->getAllIds ();
        $productCount = 1;
        /**
         * To update product status based on seller subscription plan
         */
        foreach ( $sellerProductIds as $sellerProductId ) {
            /**
             * Checking for product enable or disable option
             */
            if ($maxProductCount >= $productCount && $action == 'enable' || $maxProductCount == 'unlimited' && $action == 'enable') {
                $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $sellerProductId )->setProductApproval ( 1 )->save ();
            } else {
                $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->load ( $sellerProductId )->setProductApproval ( 0 )->setStatus ( 0 )->save ();
            }
            /**
             * Iterating the product count for subscription product limit
             */
            $productCount = $productCount + 1;
        }
    }
}