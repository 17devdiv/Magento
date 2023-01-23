<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Products;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Dotsquares\Marketplace\Controller\Adminhtml\Sellers;

class Massapprove extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @param Context $context            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(Context $context, PageFactory $resultPageFactory) {
        parent::__construct ( $context );
        $this->resultPageFactory = $resultPageFactory;
    }
    /**
     * Index action
     *
     * @return void
     */
    public function execute() {
        $result = $this->getRequest ()->getParam ( 'id' );
        foreach ( $result as $approvalProductId ) {
            try {
                $customerObject = $this->_objectManager->get ( '\Magento\Catalog\Model\Product' );
                $customerObject->load ( $approvalProductId )->setStatus ( 1 )->setProductApproval ( 1 )->save ();
                $sellerDetails = $customerObject->load ( $approvalProductId );
                $customerId = $sellerDetails->getSellerId ();
                $productName = $sellerDetails->getName ();
                $sellerDetails = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
                $sellerDetails->load ( $customerId );
                $sellerName = $sellerDetails->getFirstname ();
                $sellerEmail = $sellerDetails->getEmail ();
                $receiverInfo = [ 
                        'name' => $sellerName,
                        'email' => $sellerEmail 
                ];
                $templateId = 'marketplace_product_approval_template';
                $emailTempVariables = array ();
                $emailTempVariables ['name'] = $sellerName;
                $emailTempVariables ['productname'] = $productName;
                $adminInfo = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
                $adminEmail = $adminInfo->getAdminEmail ();
                $admin = $adminInfo->getAdminName ();
                $senderInfo = [ 
                        'name' => $admin,
                        'email' => $adminEmail 
                ];
                $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/products/index' );
    }
}