<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Review;

use Dotsquares\Marketplace\Controller\Adminhtml\Review;

class Index extends Review {
    /**
     *
     * @return void
     */
    public function execute() {
        if ($this->getRequest ()->getQuery ( 'ajax' )) {
            $this->_forward ( 'grid' );
            return;
        }
        
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        /**
         * Enable menu
         */
        $resultPage->setActiveMenu ( 'Dotsquares_Marketplace::main_menu' );
        /**
         * Set title
         */
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Manage Seller Review' ) );
        /**
         * Rertun view page
         */
        return $resultPage;
    }
}
