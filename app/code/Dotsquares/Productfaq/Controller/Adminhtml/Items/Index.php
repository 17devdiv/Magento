<?php

namespace Dotsquares\Productfaq\Controller\Adminhtml\Items;

class Index extends \Dotsquares\Productfaq\Controller\Adminhtml\Items
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Dotsquares_Productfaq::productfaq');
        $resultPage->getConfig()->getTitle()->prepend(__("Product's Faq"));
        $resultPage->addBreadcrumb(__('Dotsquares'), __('Dotsquares'));
        $resultPage->addBreadcrumb(__("Product's Faq"), __("Product's Faq"));
        return $resultPage;
    }
}
