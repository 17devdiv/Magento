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

class Index extends Transactions {
    /**
     * Execute the result
     * 
     * @return void
     */
    public function execute() {
        if ($this->getRequest ()->getQuery ( 'ajax' )) {
            $this->_forward ( 'grid' );
            return;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        $resultPage->setActiveMenu ( 'Dotsquares_Marketplace::main_menu' );
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Manage Seller Orders' ) );
        return $resultPage;
    }
}
