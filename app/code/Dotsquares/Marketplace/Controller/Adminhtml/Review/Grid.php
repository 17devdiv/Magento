<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Review;

use Dotsquares\Marketplace\Controller\Adminhtml\Review;

/**
 * This class contains the seller review grid functionality
 */
class Grid extends Review {
    /**
     * Prepare seller review collection
     */
    protected function _prepareCollection() {
        /**
         * Get collection
         */
        $collection = $this->_gridFactory->create ()->getCollection ();
        /**
         * Set collection
         */
        $this->setCollection ( $collection );
        parent::_prepareCollection ();
        /**
         * Return object
         */
        return $this;
    }
    /**
     *
     * @return void
     */
    public function execute() {
        /**
         * Create factory for review
         */
        return $this->_resultPageFactory->create ();
    }
}
