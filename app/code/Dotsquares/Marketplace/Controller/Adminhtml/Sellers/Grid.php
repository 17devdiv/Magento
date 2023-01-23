<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Sellers;

use Dotsquares\Marketplace\Controller\Adminhtml\Sellers;

class Grid extends Sellers {
    protected function _prepareCollection() {
        $collection = $this->_gridFactory->create ()->getCollection ();
        $this->setCollection ( $collection );
        parent::_prepareCollection ();
        return $this;
    }
    /**
     *
     * @return void
     */
    public function execute() {
        return $this->_resultPageFactory->create ();
    }
}
