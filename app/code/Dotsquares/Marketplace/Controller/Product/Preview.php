<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Product;

/**
 * This class contains product preview functions
 */
class Preview extends \Magento\Framework\App\Action\Action {
    
    /**
     * Marketplace helper data object
     *
     * @var \Dotsquares\Marketplace\Helper\Data
     */
    protected $dataHelper;
    /**
     * Constructor
     *
     * \Dotsquares\Marketplace\Helper\Data $dataHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Load add product/edit product page
     *
     * @return void
     */
    public function execute() {
        /**
         * Get Customer Session
         *
         * @var unknown
         */
        $this->_view->loadLayout ();
        $this->_view->renderLayout ();
    }
}
