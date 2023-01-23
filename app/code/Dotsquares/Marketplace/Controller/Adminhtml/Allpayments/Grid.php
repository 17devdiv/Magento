<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Allpayments;

use Dotsquares\Marketplace\Controller\Adminhtml\Allpayments;

class Grid extends Allpayments {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * To create result page for seller payments grid
         */
        return $this->_resultPageFactory->create ();
    }
}
