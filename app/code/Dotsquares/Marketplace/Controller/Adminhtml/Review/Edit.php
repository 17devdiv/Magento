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
namespace Dotsquares\Marketplace\Controller\Adminhtml\Review;

use Dotsquares\Marketplace\Controller\Adminhtml\Review;

class Edit extends Review {
    /**
     * Seller review edit action
     */
    public function execute() {
        $reviewId = $this->getRequest ()->getParam ( 'id' );
     
        $reviewModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Review' );
        /**
         * Checking for review exist or not
         */
        if ($reviewId) {
            $reviewModel->load ( $reviewId );
            if (! $reviewModel->getId ()) {
                $this->messageManager->addError ( __ ( 'This Review no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        /**
         * Restore previously entered form data from session
         */
        $reviewData = $this->_session->getNewsData ( true );
        /**
         * Setting review data
         */
        if (! empty ( $reviewData )) {
            $reviewModel->setData ( $reviewData );
        }
        $this->_coreRegistry->register ( 'marketplace_review', $reviewModel );
        /** @var \Magento\Backend\Model\View\Result\Page $resultHtml */
        $resultHtml = $this->_resultPageFactory->create ();
        /**
         * Activate menu
         */
        $resultHtml->setActiveMenu ( 'Dotsquares_Marketplace::main_menu' );
        /**
         * To set title
         */
        $resultHtml->getConfig ()->getTitle ()->prepend ( __ ( 'Edit Seller Review' ) );
        /**
         * Return result html page
         */
        return $resultHtml;
    }
}
