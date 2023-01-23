<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionplans;

use Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionplans;

class Index extends Subscriptionplans {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Checking request for grid
         */
        if ($this->getRequest ()->getQuery ( 'ajax' )) {
            $this->_forward ( 'grid' );
            return;
        }
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        $resultPage->setActiveMenu ( 'Dotsquares_Marketplace::main_menu' );
        /**
         * Setting title for subscription plan grid
         */
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Manage Subscription Plans' ) );
        /**
         * Return result page
         */
        return $resultPage;
    }
}
