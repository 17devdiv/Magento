<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Seller;

/**
 * This class contains loading all seller functions
 */
class Allseller extends \Magento\Framework\App\Action\Action {
    /**
     * Load Page Layout
     *
     * @return void
     */
    public function execute() {
        $this->_view->loadLayout ();
        $this->_view->renderLayout ();
    }
}
