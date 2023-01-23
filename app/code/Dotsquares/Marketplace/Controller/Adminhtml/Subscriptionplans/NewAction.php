<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
/**
 * This class contains seller subscription plan add functionality
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionplans;

use Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionplans;

/**
 * This class contains for new subscription plan action
 */
class NewAction extends Subscriptionplans {
    /**
     * Seller review add action
     */
    public function execute() {
        /**
         * Redirect to edit subscription plan page
         */
        $this->_redirect ( '*/*/edit' );
    }
}
