<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 * */
/**
 * Recipient email config path
 */
namespace Dotsquares\Marketplace\Controller\Contact;

use \Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Send contact email to seller
 */
class Savedata extends \Magento\Framework\App\Action\Action {
    /**
     * Post user question
     *
     * @return void
     */
    public function execute() {
        $subject = $this->getRequest ()->getPost ( 'subject' );
        /**
         * get message
         */
        $message = $this->getRequest ()->getPost ( 'message' );
        
        $seller = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
        $adminEmail = $seller->getAdminEmail ();
        $admin = $seller->getAdminName ();
        $receiverInfo = [ 
                'name' => $admin,
                'email' => $adminEmail 
        ];
        
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        
        if ($customerSession->isLoggedIn ()) {
            $customerId = $customerSession->getCustomerId ();
            $customerEmail = $customerSession->getCustomer ()->getEmail ();
            $customerName = $customerSession->getCustomer ()->getName ();
            $sellerDatas = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
            $sellerDetails = $sellerDatas->load ( $customerId, 'customer_id' );
            $sellerContact = $sellerDetails->getContact ();
        } else {
            $sellerContact = '';
            $customerName = '';
            $customerEmail = '';
        }
        $senderInfo = [ 
                'name' => $customerName,
                'email' => $customerEmail 
        ];
        $emailTempVariables ['subject'] = $subject;
        $emailTempVariables ['message'] = $message;
        $emailTempVariables ['contact'] = $sellerContact;
        $emailTempVariables ['name'] = $customerName;
        $emailTempVariables ['email'] = $customerEmail;
        
        /*
         * We write send mail function in helper because if we want to
         * use same in other action then we can call it directly from helper
         */
        /* call send mail method from helper or where you define it */
        
        $templateId = 'marketplace_seller_contact_admin_template';
        $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
        $this->messageManager->addSuccess ( __ ( 'Thanks for contacting us with your comments and questions. We\'ll respond to you very soon' ) );
        $this->_redirect ( '*/contact/form' );
    }
}
