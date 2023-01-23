<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Payments;

use Dotsquares\Marketplace\Controller\Adminhtml\Payments;

/**
 * This class contains seller payments grid functionaity
 */
class Index extends Payments {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Checking for request query using ajax
         */
        if ($this->getRequest ()->getQuery ( 'ajax' )) {
            /**
             * To forward to grid controller
             */
            $this->_forward ( 'grid' );
            return;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        /**
         * To set active menu
         */
        $resultPage->setActiveMenu ( 'Dotsquares_Marketplace::main_menu' );
        /**
         * Setting title for subscription plan grid
         */
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Manage Seller Payments' ) );
        /**
         * Return result page
         */
        return $resultPage;
    }
}
