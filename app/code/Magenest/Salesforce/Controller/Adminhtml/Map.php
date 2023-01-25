<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Salesforce extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_Salesforce
 * @author   ThaoPV
 */

namespace Magenest\Salesforce\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magenest\Salesforce\Model\MapFactory;
use Magenest\Salesforce\Model\ResourceModel\Map as MapResource;
use Magenest\Salesforce\Model\ResourceModel\Map\CollectionFactory as MapCollectionFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Reviews admin controller
 *
 * Class Map
 * @package Magenest\Salesforce\Controller\Adminhtml
 */
abstract class Map extends Action
{
    /** @var string  */
    const ADMIN_RESOURCE = 'Magenest_Salesforce::mapping';
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Map model factory
     *
     * @var \Magenest\Salesforce\Model\MapFactory
     */
    protected $_mapFactory;

    /** @var MapResource  */
    protected $_mapResource;

    /**
     * Map Collection factory
     *
     * @var \Magenest\Salesforce\Model\MapFactory
     */
    protected $_collectionFactory;

    /** @var \Magenest\Salesforce\Logger\Logger  */
    protected $_logger;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Map constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param MapFactory $mapFactory
     * @param MapResource $mapResource
     * @param MapCollectionFactory $collectionFactory
     * @param \Magenest\Salesforce\Logger\Logger $logger
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        MapFactory $mapFactory,
        MapResource $mapResource,
        MapCollectionFactory $collectionFactory,
        \Magenest\Salesforce\Logger\Logger $logger,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig       = $scopeConfig;
        $this->coreRegistry       = $coreRegistry;
        $this->_mapFactory        = $mapFactory;
        $this->_mapResource = $mapResource;
        $this->_collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        $this->resultPageFactory  = $resultPageFactory;
        $this->_construct();
        parent::__construct($context);
    }

    /**
     * execute after construct
     */
    protected function _construct()
    {
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Salesforce::salesforce');
        $resultPage->addBreadcrumb(__('Manage Mapping'), __('Manage Mapping'));

        return $resultPage;
    }
}
