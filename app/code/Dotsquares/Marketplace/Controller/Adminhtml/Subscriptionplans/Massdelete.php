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

/**
 * This class contains the subscription plan mass delete functionality.
 */
class Massdelete extends Subscriptionplans {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting plan id for mass delete
         */
        $enableIds = $this->getRequest ()->getParam ( 'selected' );
        /**
         * Iterate plan id
         */
        foreach ( $enableIds as $enableId ) {
            try {
                /**
                 * Create object for subscription plans
                 */
                $subscriptionPlanObj = $this->_objectManager->get ( '\Dotsquares\Marketplace\Model\Subscriptionplans' );
                /**
                 * Delete selected plan by plan id
                 */
                $subscriptionPlanObj->load ( $enableId )->delete ();
            } catch (\Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Checking for plan count
         */
        if (count ( $enableIds )) {
            /**
             * Setting sessio message for subscription plan delete
             */
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were deleted.', count ( $enableIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
