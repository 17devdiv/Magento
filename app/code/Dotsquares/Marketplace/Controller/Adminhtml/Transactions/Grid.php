<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Transactions;

use Dotsquares\Marketplace\Controller\Adminhtml\Transactions;

class Grid extends Transactions {
    /**
     *
     * @return void
     */
    public function execute() {
        return $this->_resultPageFactory->create ();
    }
}
