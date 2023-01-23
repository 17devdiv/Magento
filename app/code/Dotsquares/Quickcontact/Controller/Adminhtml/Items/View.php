<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Quickcontact\Controller\Adminhtml\Items;

class View extends \Dotsquares\Quickcontact\Controller\Adminhtml\Items
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Dotsquares\Quickcontact\Model\Items');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                $this->_redirect('dotsquares_quickcontact/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_dotsquares_quickcontact_items', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('items_items_view');
        $this->_view->renderLayout();
    }
}
