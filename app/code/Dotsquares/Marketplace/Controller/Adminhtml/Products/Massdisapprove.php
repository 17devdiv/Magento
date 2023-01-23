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

class Massdisapprove extends \Magento\Backend\App\Action {
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
                $customerFactory = $this->_objectManager->get ( '\Magento\Catalog\Model\Product' );
                $customerFactory->load ( $approvalProductId )->setStatus ( 2 )->setProductApproval ( 0 )->save ();
                $sellerDetails = $customerFactory->load ( $approvalProductId );
                $customerId = $sellerDetails->getSellerId ();
                $productName = $sellerDetails->getName ();
                $customer = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' );
                $customer->load ( $customerId );
                $sellerName = $customer->getFirstname ();
                $sellerEmail = $customer->getEmail ();
                $receiverInfo = [ 
                        'name' => $sellerName,
                        'email' => $sellerEmail 
                ];
                
                $adminObject = $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
                $senderInfo = [ 
                        'name' => $adminObject->getAdminName (),
                        'email' => $adminObject->getAdminEmail () 
                ];
                /**
                 * Assign values for your template variables
                 */
                $emailTempVariables = array ();
                $emailTempVariables ['name'] = $sellerName;
                $emailTempVariables ['productname'] = $productName;
                
                $templateId = 'marketplace_product_disapproval_template';
                $this->_objectManager->get ( 'Dotsquares\Marketplace\Helper\Email' )->yourCustomMailSendMethod ( $emailTempVariables, $senderInfo, $receiverInfo, $templateId );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
}