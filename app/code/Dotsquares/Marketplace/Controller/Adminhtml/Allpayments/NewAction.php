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
namespace Dotsquares\Marketplace\Controller\Adminhtml\Allpayments;

use Dotsquares\Marketplace\Controller\Adminhtml\Allpayments;

/**
 * This class contains for new subscription plan action
 */
class NewAction extends Allpayments {
    /**
     * Seller review add action
     */
    public function execute() {
        /**
         * Redirect to edit subscription plan page
         */
        $this->_redirect ( '*/payments/index' );
    }
}
