<?php


namespace Dotsquares\SubscriberRebate\Controller\Adminhtml\Program;


class Index extends
    \Dotsquares\SubscriberRebate\Controller\Adminhtml\AbstractAction
{

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Dotsquares_SubscriberRebate::newsletterdiscountpro');
        $resultPage->addBreadcrumb(__('Subscription Discount'), __('Subscription Discount'));
        $resultPage->addBreadcrumb(__('Programs'), __('Programs'));
        $resultPage->getConfig()->getTitle()->prepend(__('Programs'));
        return $resultPage;
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dotsquares_SubscriberRebate::program');
    }
}