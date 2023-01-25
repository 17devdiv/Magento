<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Queue;

use Magenest\Salesforce\Helper\Data;
use Magento\Sales\Model\ResourceModel\Sale\CollectionFactory;

abstract class AbstractionAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magenest_Salesforce::config_salesforce';

    /** @var Data  */
    protected $_helperData;

    /** @var \Magenest\Salesforce\Model\QueueFactory  */
    protected $queueFactory;

    /** @var \Magento\Framework\View\Result\PageFactory  */
    protected $resultPageFactory;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    protected $_configInterface;

    /** @var \Magento\Config\Model\ResourceModel\Config  */
    protected $_config;

    /** @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory  */
    protected $customerCollection;

    /** @var \Magento\Framework\Serialize\Serializer\Json  */
    protected $_json;

    /** @var \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory */
    protected $_ruleColFactory;

    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory  */
    protected $_orderColFactory;

    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  */
    protected $_productColFactory;

    /**
     * AbstractionAction constructor.
     *
     * @param Data $helperData
     * @param \Magenest\Salesforce\Model\QueueFactory $queueFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleColFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderColFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productColFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magenest\Salesforce\Helper\Data $helperData,
        \Magenest\Salesforce\Model\QueueFactory $queueFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config $config,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleColFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderColFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productColFactory,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_helperData = $helperData;
        $this->queueFactory = $queueFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_configInterface = $scopeConfig;
        $this->_config = $config;
        $this->customerCollection = $customerCollection;
        $this->_json = $json;
        $this->_ruleColFactory = $ruleColFactory;
        $this->_orderColFactory = $orderColFactory;
        $this->_productColFactory = $productColFactory;
        parent::__construct($context);
    }

}