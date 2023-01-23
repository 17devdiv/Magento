<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Products;

use Dotsquares\Marketplace\Controller\Adminhtml\Products;

class Grid extends Products {
    /**
     *
     * @return void
     */
    public function execute() {
        return $this->_resultPageFactory->create ();
    }
}
