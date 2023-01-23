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

class Delete extends Subscriptionplans {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Getting delete id
         */
        $deleteId = $this->getRequest ()->getParam ( 'id' );
        try {
            /**
             * Creating a object for subscriptionplans
             */
            $subscriptionPlanObj = $this->_objectManager->get ( '\Dotsquares\Marketplace\Model\Subscriptionplans' );
            /**
             * Delete subscriptionplan
             */
            $subscriptionPlanObj->load ( $deleteId )->delete ();
        } catch (\Exception $e ) {
            $this->messageManager->addError ( $e->getMessage () );
        }
        /**
         * Setting a session success message and redirect to subscriptionplans grid page
         */
        $this->messageManager->addSuccess ( __ ( 'The data has been deleted successfully.' ) );
        $this->_redirect ( '*/*/index' );
    }
}
