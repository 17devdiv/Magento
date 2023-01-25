<?php

namespace Magenest\Salesforce\Controller\Adminhtml\Map;

use Magenest\Salesforce\Controller\Adminhtml\Map;

/**
 * Class NewMapping
 * @package Magenest\Salesforce\Controller\Adminhtml\Map
 */
class NewMapping extends Map
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Mapping Management'));
        return $resultPage;
    }
}