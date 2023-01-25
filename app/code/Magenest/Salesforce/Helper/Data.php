<?php

namespace Magenest\Salesforce\Helper;

use Magenest\Salesforce\Model\Connector;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Data
 * @package Magenest\Salesforce\Helper
 */
class Data extends AbstractHelper
{
    const MODULE_NAME                           = 'Magenest_Salesforce';
    const CONFIGURATION_SALESFORCE_SECTION_PATH = 'adminhtml/system_config/edit/section/salesforcecrm';
    const CONFIGURATION_IS_CONNECTED            = 'salesforcecrm/config/is_connected';
    const XML_PATH_INSTANCE_URL                 = 'salesforcecrm/config/instance_url';
    const XML_PATH_SALESFORCE_EMAIL             = 'salesforcecrm/config/email';
    const XML_PATH_SALESFORCE_PASSWD            = 'salesforcecrm/config/passwd';
    const XML_PATH_SALESFORCE_SECURITY_TOKEN    = 'salesforcecrm/config/security_token';
    const XML_SYNC_CONFIGURATION_PATH           = 'salesforcecrm/sync/';
    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory  */
    protected $_orderColFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ModuleListInterface $moduleList
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderColFactory
     */
    public function __construct(
        Context $context,
        ModuleListInterface $moduleList,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderColFactory
    ){
        parent::__construct($context);
        $this->moduleList = $moduleList;
        $this->_orderColFactory = $orderColFactory;
    }

    /**
     * @return array|null
     */
    public function getModule()
    {
        return $this->moduleList->getOne(self::MODULE_NAME);
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        return isset($this->getModule()['setup_version']) ? $this->getModule()['setup_version'] : '';
    }

    /**
     * @return bool
     */
    public function isSalesForceCRMConnected()
    {
        return (bool)$this->scopeConfig->getValue(self::CONFIGURATION_IS_CONNECTED);
    }

    /**
     * @return mixed
     */
    public function getInstanceUrl()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INSTANCE_URL);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SALESFORCE_EMAIL);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SALESFORCE_PASSWD);
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SALESFORCE_SECURITY_TOKEN);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function getEnabledSyncConfig($type)
    {
        return (bool)$this->scopeConfig->getValue(self::XML_SYNC_CONFIGURATION_PATH . $type);
    }

    /**
     * @param string $type
     * @return int
     */
    public function getSyncConfigMode($type)
    {
        return $this->scopeConfig->getValue(self::XML_SYNC_CONFIGURATION_PATH . $type . '_mode');
    }

    /**
     * @param string $type
     * @return int
     */
    public function getSyncConfigTime($type)
    {
        return $this->scopeConfig->getValue(self::XML_SYNC_CONFIGURATION_PATH . $type . '_time');
    }

    /**
     * @return mixed
     */
    public function getConnectEnvironment()
    {
        return $this->scopeConfig->getValue(Connector::XML_PATH_SALESFORCE_CONNECT_ENVIRONMENT);
    }

    /**
     * @param int $customerId
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getCustomerOrderCollection($customerId)
    {
        return $this->_orderColFactory->create()->addFieldToFilter('customer_id', $customerId);
    }
}
