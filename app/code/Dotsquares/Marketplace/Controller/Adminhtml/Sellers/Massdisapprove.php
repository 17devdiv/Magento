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

class Massdisapprove extends Sellers {
    /**
     *
     * @return void
     */
    public function execute() {
        $disApprovalIds = $this->getRequest ()->getParam ( 'approve' );
        foreach ( $disApprovalIds as $disApprovalId ) {
            try {
                $customerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
                $customerModelCollection = $customerModel->getCollection ()->addFieldToFilter( 'id',$disApprovalId);
                $customerDatas = $customerModelCollection->getData ();
                $customerId = $customerDatas['0']['customer_id'];
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
                $productCollection = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $customerId );
                foreach ( $productCollection as $productCollections ) {
                    $id = $productCollections->getEntityId ();
                    $productCollections->load ( $id );
                    $sellerId = $productCollections->getSellerId ();
                    if ($customerId == $sellerId) {
                        $productCollections->setStatus ( 2 )->save ();
                    }
                }
                /** @var $newsModel \Mageworld\SimpleNews\Model\News */
                $customerFactory = $this->_objectManager->get ( '\Dotsquares\Marketplace\Model\Seller' );
                $customerFactory->load ( $disApprovalId )->setStoreId ( 0 )->setStatus ( 0 )->save ();
                $sellerDetails = $customerFactory->load ( $disApprovalId );
                $customerId = $sellerDetails->getCustomerId ();
                $customerObject = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
                $customerObject->load ( $customerId );
                $sellerName = $customerObject->getFirstname ();
                $sellerEmail = $customerObject->getEmail ();
                $receiverData = [
                        'name' => $sellerName,
                        'email' => $sellerEmail
                ];
                /**
                 * Assign values for your template variables
                 */
                $emailTempVariables = array ();
                $emailTempVariables ['cname'] = $sellerName;
                $adminData = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
                $adminEmail = $adminData->getAdminEmail ();
                $adminName = $adminData->getAdminName ();
                /**
                 * Sender Detail
                 */
                $senderData = [
                        'name' => $adminName,
                        'email' => $adminEmail
                ];
                $templateId = 'marketplace_seller_admin_disapproval_template';
                $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderData, $receiverData, $templateId );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        if (count ( $disApprovalIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were disapproved.', count ( $disApprovalIds ) ) );
            $this->messageManager->addSuccess ( __ ( 'Seller Products also Disabled' ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}