<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionprofiles;

use Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionprofiles;

class Grid extends Subscriptionprofiles {
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * To create result page for subscription profiles grid
         */
        return $this->_resultPageFactory->create ();
    }
}
