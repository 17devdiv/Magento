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

class Massapprove extends Review {

    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Get list of ids
         */
        $approvalIds = $this->getRequest ()->getParam ( 'approve' );
        /**
         * Iterate ids
         */
        foreach ( $approvalIds as $approvalId ) {
            try {
                /**
                 * Update review status
                 */
                $review = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Review' );
                $review->load ( $approvalId )->setStatus ( 1 )->save ();
                
                /**
                 * Send notification
                 */
                $reviewDetails = $review->load ( $approvalId );
                $templateIdValue = 'marketplace_review_admin_approval_template';
                $this->_objectManager->get ( 'Dotsquares\Marketplace\Controller\Adminhtml\Review\Save' )->sendReviewNotification ( $reviewDetails, $templateIdValue );
            } catch ( \Exception $e ) {
                /**
                 * Adding session error message
                 */
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Checking for updated review count
         */
        if (count ( $approvalIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were approved.', count ( $approvalIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
