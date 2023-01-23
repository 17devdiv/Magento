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

class MassDelete extends Sellers {
    /**
     *
     * @return voids
     */
    public function execute() {
        $sellerIds = $this->getRequest ()->getParam ( 'approve' );
        $customersDeleted = 0;
        foreach ( $sellerIds as $customerId ) {
            $customerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
            $customerModelCollection = $customerModel->getCollection ()->addFieldToFilter( 'id',$customerId );
            $customerDatas = $customerModelCollection->getData ();
            $customerModel->load($customerId)->delete ();
            $customerId = $customerDatas['0']['customer_id'];
            /**
             * Disable the seller products.
             */
                $sellerFlag = 4;
                $sellerProductCollection = $this->_objectManager->get ( 'Magento\Catalog\Model\Product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $customerId );
                $sellerProductDatas = $sellerProductCollection->getData ();
                foreach ( $sellerProductDatas as $sellerProducts ) {
                    $productId = $sellerProducts ['entity_id'];
                    $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productId )->setStatus ( 2 )->setProductApproval ( 0 )->save ();
                }
            $customersDeleted ++;
        }
        if ($customersDeleted) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were deleted.', $customersDeleted ) );
            if ($sellerFlag == 4) {
                $this->messageManager->addSuccess ( __ ( 'Seller Products were disabled and disapproved' ) );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
}
