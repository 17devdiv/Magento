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
namespace Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionplans;

use Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionplans;

class Edit extends Subscriptionplans {
    /**
     * Seller review edit action
     */
    public function execute() {
        /**
         * Gettin plan id from query string
         */
       
        $planId = $this->getRequest ()->getParam ( 'id' );
         /**
         * Create object for subscriptionplans
         */
        $subscriptionModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionplans' );
        /**
         * Checking for subscription plan id exist or not
         */
        if ($planId) {
            $subscriptionModel->load ( $planId );
            if (! $subscriptionModel->getId ()) {
                $this->messageManager->addError ( __ ( 'This Subscription Plan no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        /**
         * Restore previously entered form data from session
         */
        $subscriptionPlanData = $this->_session->getNewsData ( true );
        if (! empty ( $subscriptionPlanData )) {
            $subscriptionModel->setData ( $subscriptionPlanData );
        }
        /**
         * Creaging register for subscription plan model
         */
        $this->_coreRegistry->register ( 'marketplace_subscriptionplans', $subscriptionModel );
        /** @var \Magento\Backend\Model\View\Result\Page $resultHtml */
        $resultHtml = $this->_resultPageFactory->create ();
        /**
         * Activate markeptlace menu
         */
        $resultHtml->setActiveMenu ( 'Dotsquares_Marketplace::main_menu' );
        /**
         * Setting title for subscrption plan
         */
        $resultHtml->getConfig ()->getTitle ()->prepend ( __ ( 'Edit Subscription Plans' ) );
        return $resultHtml;
    }
}
