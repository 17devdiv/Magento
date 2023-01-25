<?php

namespace Magenest\Salesforce\Controller\Adminhtml\Field;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\Salesforce\Model\FieldFactory;
use Magenest\Salesforce\Model\MapFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class AjaxGetFields
 * @package Magenest\Salesforce\Controller\Adminhtml\Field
 */
class AjaxGetFields extends Action
{
    /**
     * @var FieldFactory
     */
    protected $_fieldFactory;

    /**
     * @var MapFactory
     */
    protected $_mapFactory;

    /**
     * AjaxGetFields constructor.
     * @param FieldFactory $fieldFactory
     * @param MapFactory $mapFactory
     * @param Context $context
     */
    public function __construct(
        FieldFactory $fieldFactory,
        MapFactory $mapFactory,
        Context $context
    )
    {
        $this->_mapFactory   = $mapFactory;
        $this->_fieldFactory = $fieldFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|Raw|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $_model        = $this->_fieldFactory->create();
        $mapCollection = $this->_mapFactory->create()->getCollection();
        $out           = [];

        try {
            if ($this->getRequest()->isAjax()) {
                $data         = $this->getRequest()->getParam('type');
                $mappedFields = $mapCollection->addFieldToFilter('type', $data)->getData();

                $_model->loadByTable($data, true);
                $salesforceFields         = $_model->getSalesforceFields();
                $magentoFields            = $_model->getMagentoFields();
                $out['magento_fields']    = $magentoFields;
                $out['salesforce_fields'] = $salesforceFields;
                $out['mapped']            = $mappedFields;
            } else {
                /** @var Raw $raw */
                $raw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
                return $raw->setHttpResponseCode(400);
            }
        } catch (\Exception $e) {
        }
        return $this->getResponse()->setBody(json_encode($out));
    }
}