<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Enquiry\Controller\Adminhtml\Items;

class Index extends \Dotsquares\Enquiry\Controller\Adminhtml\Items
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
        $resultPage->setActiveMenu('Dotsquares_Enquiry::enquiry');
        $resultPage->getConfig()->getTitle()->prepend(__('Order Enquiries'));
        $resultPage->addBreadcrumb(__('Order Enquiries'), __('Order Enquiries'));
        $resultPage->addBreadcrumb(__('Order Enquiries'), __('Order Enquiries'));
        return $resultPage;
    }
}
