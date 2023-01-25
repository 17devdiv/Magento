<?php
/**
 * * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Salesforce\Controller\Adminhtml\System\Config\Getauth;

use Magenest\Salesforce\Model\Connector;
use Magento\Backend\App\Action;
use Magento\Config\Model\ResourceModel\Config as ConfigModel;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class GetAuth
 * @package Magenest\Salesforce\Controller\Adminhtml\System\Config\Getauth
 */
class Disconnect extends Action
{
    /** @var ConfigModel  */
    protected $_configModel;

    /** @var ScopeConfigInterface  */
    protected $_scopeConfig;

    /** @var \Magento\Framework\App\Cache\TypeListInterface  */
    protected $_cacheTypeList;

    /** @var \Magento\Framework\App\Cache\Frontend\Pool  */
    protected $_cacheFrontendPool;

	/**
	 * Disconnect constructor.
	 * @param Action\Context $context
	 * @param ScopeConfigInterface $scopeConfig
	 * @param ConfigModel $configModel
	 * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
	 * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
	 */
    public function __construct(
        Action\Context $context,
        ScopeConfigInterface $scopeConfig,
        ConfigModel $configModel,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
        parent::__construct($context);
        $this->_configModel = $configModel;
        $this->_scopeConfig = $scopeConfig;
	    $this->_cacheTypeList = $cacheTypeList;
	    $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_configModel->saveConfig(Connector::XML_PATH_SALESFORCE_IS_CONNECTED, 0, 'default', 0);
        $this->_configModel->saveConfig(Connector::XML_PATH_SALESFORCE_ACCESS_TOKEN, null, 'default', 0);
        $this->_configModel->saveConfig(Connector::XML_PATH_SALESFORCE_INSTANCE_URL, null, 'default', 0);
        $this->cleanConfig();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Salesforce::config_salesforce');
    }

    private function cleanConfig()
    {
	    $types = array('config', 'full_page');
	    foreach ($types as $type) {
            $this->_cacheTypeList->invalidate($type);
	    }
	    foreach ($this->_cacheFrontendPool as $cacheFrontend) {
		    $cacheFrontend->getBackend()->clean();
	    }
    }
}
