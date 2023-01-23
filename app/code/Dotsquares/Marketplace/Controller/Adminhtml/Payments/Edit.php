<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
/**
 * This class contains seller review edit functionality
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Payments;

use Dotsquares\Marketplace\Controller\Adminhtml\Payments;

class Edit extends Payments {
    /**
     * Seller review edit action
     */
    public function execute() {
        /**
         * Gettin plan id from query string
         */
        $profileId = $this->getRequest ()->getParam ( 'id' );
       
        /**
         * Create object for subscriptionplans
         */
        $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        /**
         * Checking for subscription plan id exist or not
         */
        if ($profileId) {
            $sellerModel->load ( $profileId );
            if (! $sellerModel->getId ()) {
                $this->messageManager->addError ( __ ( 'This Subscription Plan no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        /**
         * Restore previously entered form data from session
         */
        $paymentsData = $this->_session->getNewsData ( true );
        if (! empty ( $paymentsData )) {
            $sellerModel->setData ( $paymentsData );
        }
        /**
         * Creaging register for subscription plan model
         */
        $this->_coreRegistry->register ( 'marketplace_payments', $sellerModel );
        /** @var \Magento\Backend\Model\View\Result\Page $resultHtml */
        $resultHtml = $this->_resultPageFactory->create ();
        /**
         * Activate markeptlace menu
         */
        $resultHtml->setActiveMenu ( 'Dotsquares_Marketplace::main_menu' );
        /**
         * Setting title for subscrption plan
         */
        $resultHtml->getConfig ()->getTitle ()->prepend ( __ ( 'Pay' ) );
        return $resultHtml;
    }
}
