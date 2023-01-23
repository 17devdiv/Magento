<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Quickcontact\Controller\Adminhtml\Items;

class Index extends \Dotsquares\Quickcontact\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Dotsquares_Quickcontact::quickcontact');
        $resultPage->getConfig()->getTitle()->prepend(__('Dotsquares Quickcontact'));
        $resultPage->addBreadcrumb(__('Dotsquares'), __('Dotsquares'));
        $resultPage->addBreadcrumb(__('Quickcontact'), __('Quickcontact'));
        return $resultPage;
    }
}
