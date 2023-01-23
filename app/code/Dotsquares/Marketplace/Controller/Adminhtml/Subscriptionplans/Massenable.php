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
 * This class contains mass subsacription plans enabled functionality
 */
class Massenable extends Subscriptionplans {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Select plan ids
         */
        $enableIds = $this->getRequest ()->getParam ( 'selected' );
        foreach ( $enableIds as $enableId ) {
            try {
                /**
                 * Create subscription plan object
                 */
                $subscriptionPlans = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Subscriptionplans' );
                /**
                 * To enable subscription plans
                 */
                $subscriptionPlans->load ( $enableId )->setStatus ( 1 )->save ();
            } catch ( \Exception $e ) {
                $this->messageManager->addError ( $e->getMessage () );
            }
        }
        /**
         * Enabled subscription plans count
         */
        if (count ( $enableIds )) {
            $this->messageManager->addSuccess ( __ ( 'A total of %1 record(s) were enabled.', count ( $enableIds ) ) );
        }
        $this->_redirect ( '*/*/index' );
    }
}
