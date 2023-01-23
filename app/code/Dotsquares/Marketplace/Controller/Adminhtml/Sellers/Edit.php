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

class Edit extends Sellers {
    public function execute() {
        $sellerId = $this->getRequest ()->getParam ( 'id' );
        $model = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        if ($sellerId) {
            $model->load ( $sellerId );
            if (! $model->getId ()) {
                $this->messageManager->addError ( __ ( 'This Seller no longer exists.' ) );
                $this->_redirect ( '*/*/' );
                return;
            }
        }
        // Restore previously entered form data from session
        $data = $this->_session->getNewsData ( true );
        if (! empty ( $data )) {
            $model->setData ( $data );
        }
        $this->_coreRegistry->register ( 'marketplace_seller', $model );
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create ();
        $resultPage->setActiveMenu ( 'Dotsquares_Marketplace::main_menu' );
        $resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Manage Commission' ) );
        return $resultPage;
    }
}
