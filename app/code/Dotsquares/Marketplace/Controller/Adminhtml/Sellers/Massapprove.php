<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Sellers;

use Dotsquares\Marketplace\Controller\Adminhtml\Sellers;

class Massapprove extends Sellers {
    /**
     *
     * @return void
     */
    public function execute() {
        $approvalIds = $this->getRequest ()->getParam ( 'approve' );
        foreach ( $approvalIds as $approvalId ) {
            try {
                $customer = $this->_objectManager->get ( '\Dotsquares\Marketplace\Model\Seller' );
                $customer->load ( $approvalId )->setStatus ( 1 )->save ();
                $sellerDetails = $customer->load ( $approvalId );
                $customerId = $sellerDetails->getCustomerId ();
               
                $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
                $customerSession->load ( $customerId );
                $sellerName = $customerSession->getFirstname ();
                $sellerEmail = $customerSession->getEmail ();
                /**
                 * Assign values for your template variables
                 */
                $emailTempVariables = array ();
                $emailTempVariables ['name'] = $sellerName;

                $receiverInfo = [
                        'name' => $sellerName,
                        'email' => $sellerEmail
                ];
                $seller = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
                $adminEmail = $seller->getAdminEmail ();
                $admin = $seller->getAdminName ();
                $senderInfo = [
                        'name' => $admin,
                        'email' => $adminEmail
                ];
                $templateIdValue = 'marketplace_seller_admin_approval_template';
                $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateIdValue );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        if (count ( $approvalIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were approved.', count ( $approvalIds ) ) );
            $this->messageManager->addSuccess ( __ ( 'Please Enable The Seller Products' ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
