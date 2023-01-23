<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Allpayments;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $viewresult;
    
    /**
     *
     * @param Context $context            
     * @param PageFactory $viewresult            
     */
    public function __construct(Context $context, PageFactory $viewresult) {
        parent::__construct ( $context );
        $this->viewresult = $viewresult;
    }
    
    /**
     * Index action
     *
     * @return void
     */
    public function execute() {
   
        /**
         * Create view result for subscription profiles page
         */
        $viewResult = $this->viewresult->create ();
        /**
         * To activate marektplace menu
         */
        $viewResult->setActiveMenu ( 'Dotsquares_Marketplace::manage_payments' );
        
        /**
         * Add breadcrumb for subscribed profiles
         */
        $viewResult->addBreadcrumb ( __ ( 'Seller Payments' ), __ ( 'All Payments' ) );
        
        $sellerId = $this->getRequest ()->getParam ( 'id' );
        if (empty ( $sellerId )) {
            /**
             * Setting title for subscripbed profiles
             */
            $viewResult->getConfig ()->getTitle ()->prepend ( __ ( 'All Payments' ) );
        } else {
           
            $storeName = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' )->load ( $sellerId, 'customer_id' )->getStoreName ();
            if (empty ( $storeName )) {
                $storeName = $this->_objectManager->get ( 'Magento\Customer\Model\Customer' )->load ( $sellerId )->getFirstname ();
            }
            $viewResult->getConfig ()->getTitle ()->prepend ( __ ( 'All Payments for ' ) . '"' . $storeName . '"' );
        }
        
        /**
         * Return page
         */
        return $viewResult;
    }
}
