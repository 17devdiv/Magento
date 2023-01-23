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

class MassDelete extends Review {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting review ids
         */
        $reviewIds = $this->getRequest ()->getParam ( 'approve' );
        /**
         * Iterate review ids
         */
        foreach ( $reviewIds as $reviewId ) {
            try {
                /**
                 * Create object for review
                 */
                $sellerFactory = $this->_objectManager->get ( '\Dotsquares\Marketplace\Model\Review' );
                /**
                 * Delete review by review id
                 */
                $sellerFactory->load ( $reviewId )->delete ();
            } catch (\Exception $e ) {
                /**
                 * Set session message
                 */
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * To show deleted review count in session message
         */
        if (count ( $reviewIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were deleted.', count ( $reviewIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
