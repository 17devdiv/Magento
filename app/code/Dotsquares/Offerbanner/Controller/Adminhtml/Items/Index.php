<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Offerbanner\Controller\Adminhtml\Items;

class Index extends \Dotsquares\Offerbanner\Controller\Adminhtml\Items
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
        $resultPage->setActiveMenu('Dotsquares_Offerbanner::offerbanner');
        $resultPage->getConfig()->getTitle()->prepend(__('Offer Banner Items'));
        $resultPage->addBreadcrumb(__('Dotsquares'), __('Offer Banner'));
        $resultPage->addBreadcrumb(__('Items'), __('Items'));
        return $resultPage;
    }
}
