<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Dotsquares\Marketplace\Model\Seller;

abstract class Sellers extends Action {
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     *
     * @param Context $context            
     * @param Registry $coreRegistry            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(Context $context, Registry $coreRegistry, PageFactory $resultPageFactory) {
        parent::__construct ( $context );
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Marketplace Seller access rights checking
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed ( 'Dotsquares_Marketplace::manage_sellers' );
    }
}
