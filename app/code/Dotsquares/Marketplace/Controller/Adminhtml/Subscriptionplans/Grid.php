<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionplans;

use Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionplans;

/**
 * This class contains the seller review grid functionality
 */
class Grid extends Subscriptionplans {
    /**
     * Prepare seller review collection
     */
    protected function _prepareCollection() {
        /**
         * Getting factory collection for grid
         */
        $collection = $this->_gridFactory->create ()->getCollection ();
        /**
         * Setting collection for grid
         */
        $this->setCollection ( $collection );
        /**
         * Calling parent prepare collection function
         */
        parent::_prepareCollection ();
        return $this;
    }
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * To create request page
         */
        return $this->_resultPageFactory->create ();
    }
}
