<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Seller;


/**
 * This class contains send seller contact email functions
 */
class Savesellercontact extends \Magento\Framework\App\Action\Action {
    /**
     * Execute the result
     *
     * @param
     *            sellerid,customer name,seller email,customer email,
     * @return void
     */
    public function execute() {
        $sellerId = $this->getRequest ()->getPost ( 'seller_id' );
        $customerName = $this->getRequest ()->getPost ( 'customer_name' );
        $customerEmail = $this->getRequest ()->getPost ( 'customer_email' );
        $customerMessage = $this->getRequest ()->getPost ( 'message' );
        $sellerName = $this->getRequest ()->getPost ( 'seller_name' );
        $sellerEmail = $this->getRequest ()->getPost ( 'seller_email' );
        
        $receiverInfo = [ 
                'name' => $sellerName,
                'email' => $sellerEmail 
        ];
        /* Sender Detail */
        
        
        $senderInfo = [ 
                'name' => $customerName,
                'email' => $customerEmail 
        ];
        $emailTempVariables ['name'] = $customerName;
        $emailTempVariables ['email'] = $customerEmail;
        $emailTempVariables ['message'] = $customerMessage;
        $emailTempVariables ['sellername'] = $sellerName;
        $sessionSellerId ='';
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
           $sessionSellerId = $customerSession->getCustomer()->getId();
        }
        
        if($sellerId == $sessionSellerId || empty($sessionSellerId)){
        $this->messageManager->addError( __ ( "Can't able to contact the own seller" ) );
        } else{
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        $templateId = 'marketplace_seller_contact_seller_template';
        
        $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
        $this->messageManager->addSuccess ( __ ( 'Thanks for contacting us with your comments and questions. We\'ll respond to you very soon' ) );
        }
        $targetPath = 'marketplace/seller/displayseller/id/' . $sellerId;
        $mainUrlRewrite = $this->_objectManager->get ( 'Magento\UrlRewrite\Model\UrlRewrite' )->load ( $targetPath, 'target_path' );
        $requestPath = $mainUrlRewrite->getRequestPath ();
        
        $id = null;
        $manager = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' );
        $store = $manager->getStore ( $id );
        $storeId = $store->getStoreId ();
        
        $baseUrl = $this->_objectManager->get ( 'Magento\Store\Model\StoreManagerInterface' )->getStore ( $storeId )->getBaseUrl ();
        
        $this->_redirect ( $baseUrl . $requestPath );
    }
}
