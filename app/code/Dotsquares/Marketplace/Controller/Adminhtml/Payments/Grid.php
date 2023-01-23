<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Payments;

use Dotsquares\Marketplace\Controller\Adminhtml\Payments;

/**
 * This class contains the seller payments grid action
 */
class Grid extends Payments {
    /**
     * Prepare seller payments collection
     */
    protected function _prepareCollection() {
        /**
         * Getting factory collection for grid
         */
        $sellerPayments = $this->_gridFactory->create ()->getCollection ();
        $this->setCollection ( $sellerPayments );
        /**
         * Call parent collection
         */
        parent::_prepareCollection ();
        /**
         * Return current scope
         */
        return $this;
    }
    /**
     * Execute result page factory
     *
     * @return object
     */
    public function execute() {
        /**
         * Create result page factory
         */
        return $this->_resultPageFactory->create ();
    }
}
