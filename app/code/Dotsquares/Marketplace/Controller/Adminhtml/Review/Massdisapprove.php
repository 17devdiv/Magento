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

class Massdisapprove extends Review {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting disapproval id for seller review
         */
        $disApprovalIds = $this->getRequest ()->getParam ( 'approve' );
        foreach ( $disApprovalIds as $disApprovalId ) {
            try {
                /**
                 * Creating seller review object
                 */
                $review = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Review' );
                $review->load ( $disApprovalId )->setStatus ( 0 )->save ();
                $reviewDetails = $review->load ( $disApprovalId );
                /**
                 * Assign template value
                 */
                $templateIdValue = 'marketplace_review_admin_disapproval_template';
                /**
                 * Sending mail for notification
                 */
                $this->_objectManager->get ( 'Dotsquares\Marketplace\Controller\Adminhtml\Review\Save' )->sendReviewNotification ( $reviewDetails, $templateIdValue );
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * To display disapproved review count in session message
         */
        if (count ( $disApprovalIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were disapproved.', count ( $disApprovalIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
