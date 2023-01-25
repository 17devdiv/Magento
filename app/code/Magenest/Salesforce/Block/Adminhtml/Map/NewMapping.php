<?php

namespace Magenest\Salesforce\Block\Adminhtml\Map;

use Magento\Framework\View\Element\Template;
use Magenest\Salesforce\Model\FieldFactory;
use Magenest\Salesforce\Model\MapFactory;
use Magento\Backend\Model\Url;

/**
 * Class NewMapping
 * @package Magenest\Salesforce\Block\Adminhtml\Map
 */
class NewMapping extends Template
{
    /** @var MapFactory  */
    protected $_mapFactory;

    /** @var FieldFactory  */
    protected $_fieldFactory;

    /** @var array  */
    protected $layoutProcessors;

    /**
     * NewMapping constructor.
     *
     * @param Template\Context $context
     * @param FieldFactory $fieldFactory
     * @param MapFactory $mapFactory
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        FieldFactory $fieldFactory,
        MapFactory $mapFactory,
        array $layoutProcessors = [],
        array $data = []
    )
    {
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->layoutProcessors = $layoutProcessors;
        $this->_mapFactory = $mapFactory;
        $this->_fieldFactory = $fieldFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        if (!$this->jsLayout) {
            $this->jsLayout = [
                'components' => [
                    'mapping' => [
                        'component' => 'Magenest_Salesforce/js/view/mapping',
                        'displayArea' => 'mapping'
                    ]
                ]
            ];
        }
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'Types' => $this->getTypes(),
            'SaveMappingUrl' => $this->getSaveMappingUrl(),
            'GetFieldsUrl' => $this->getGetFieldsUrl()
        ];
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        $model = $this->_fieldFactory->create();
        $types = array_keys($model->getAllTable());
        return $types;
    }

    /**
     * @return string
     */
    public function getSaveMappingUrl()
    {
        $url = $this->getUrl('salesforce/map/savemapping');
        return $url;
    }

    /**
     * @return string
     */
    public function getGetFieldsUrl()
    {
        $url = $this->getUrl('salesforce/field/ajaxgetfields');
        return $url;
    }
}