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
 * This class contains seller page functions
 */
class Displayseller extends \Magento\Framework\App\Action\Action {
    /**
     * Load Page Layout
     *
     * @return $resultPage
     */
    public function execute() {
        $this->_view->loadLayout ();
        $this->_view->renderLayout ();
    }
}
