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
 * This class contains the subscription plan bass disable functionality
 */
class Massdisable extends Subscriptionplans {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Plan ids for disable
         */
        $disableIds = $this->getRequest ()->getParam ( 'selected' );
        /**
         * Id iteration for diable
         */
        foreach ( $disableIds as $disableId ) {
            try {
                /**
                 * Object for subascription plans
                 */
                $subscriptionPlansObj = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionplans' );
                $subscriptionPlansObj->load ( $disableId )->setStatus ( 0 )->save ();
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Count for session message
         */
        if (count ( $disableIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were disabled.', count ( $disableIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
