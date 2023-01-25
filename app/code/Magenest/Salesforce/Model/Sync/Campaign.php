<?php
namespace Magenest\Salesforce\Model\Sync;

use Magenest\Salesforce\Model\QueueFactory;
use Magenest\Salesforce\Model\ReportFactory;
use Magenest\Salesforce\Model\RequestLogFactory;
use Magento\Config\Model\ResourceModel\Config as ResourceModelConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Serialize;

class Campaign extends \Magenest\Salesforce\Model\Connector
{
    /**
     * Constants
     */
    const XML_PATH_SYNC_CAMPAIGN = 'salesforcecrm/sync/campaign';

    /** @var \Magento\CatalogRule\Model\RuleFactory */
    protected $_ruleFactory;

    /** @var \Magenest\Salesforce\Model\Sync\Job  */
    protected $_job;

    /** @var \Magenest\Salesforce\Model\Data|Data  */
    protected $_data;

    protected $existedCampaigns = null;

    protected $createCampaignIds = null;

    protected $updateCampaignIds = null;

    /** @var \Magenest\Salesforce\Model\Sync\DataGetter  */
    protected $dataGetter;

    /**
     * Campaign constructor.
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param Serialize $serialize
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceModelConfig $resourceConfig
     * @param ReportFactory $reportFactory
     * @param QueueFactory $queueFactory
     * @param RequestLogFactory $requestLogFactory
     * @param \Magenest\Salesforce\Model\Data $data
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     * @param Job $job
     * @param DataGetter $dataGetter
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        ScopeConfigInterface $scopeConfig,
        ResourceModelConfig $resourceConfig,
        ReportFactory $reportFactory,
        QueueFactory $queueFactory,
        RequestLogFactory $requestLogFactory,
        \Magenest\Salesforce\Model\Data $data,
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magenest\Salesforce\Model\Sync\Job $job,
        \Magenest\Salesforce\Model\Sync\DataGetter $dataGetter
    ){
        parent::__construct($json, $serialize, $scopeConfig, $resourceConfig, $reportFactory, $queueFactory, $requestLogFactory);
        $this->_ruleFactory = $ruleFactory;
        $this->_data        = $data;
        $this->_type        = 'Campaign';
        $this->_table       = 'catalogrule';
        $this->_job         = $job;
        $this->dataGetter   = $dataGetter;
    }

    /**
     * @param $id
     *
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function sync($id)
    {
        $rule = $this->_ruleFactory->create()->load($id);
        $name = $rule->getName();

        $id     = $this->searchRecords($this->_type, 'Name', trim($name));
        $params = $this->_data->getCampaign($rule, $this->_type);
        $params = array_replace($params, ['Name' => $name]);

        if (!$id) {
            $id = $this->createRecords($this->_type, $params, $rule->getId());
        } else {
            $this->updateRecords($this->_type, $id, $params, $rule->getId());
        }

        return $id;
    }

    /**
     * Sync All Campaigns on Magento to Salesforce
     */
    public function syncAllCampaigns()
    {
        try {
            $rules      = $this->_ruleFactory->create()->getCollection();
            $lastRuleId = $rules->getLastItem()->getId();
            $count      = 0;
            $response   = [];
            /** @var \Magento\CatalogRule\Model\Rule $rule */
            foreach ($rules as $rule) {
                $this->addRecord($rule->getId());
                $count++;
                if ($count >= 10000 || $rule->getId() == $lastRuleId) {
                    $response += $this->syncQueue();
                }
            }
            return $response;
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug($e->getMessage());
        }
        return null;
    }

    /**
     * @return array|mixed|string|null
     */
    public function syncQueue()
    {
        $createResponse = $this->createCampaigns();
        $updateResponse = $this->updateCampaigns();
        $response       = $createResponse + $updateResponse;
        $this->unsetCreateQueue();
        $this->unsetUpdateQueue();
        return $response;
    }

    /**
     * Send request to create accounts
     */
    protected function createCampaigns()
    {
        $response = [];
        if (!is_null($this->createCampaignIds)) {
            $response = $this->sendCampaignsRequest($this->createCampaignIds, 'insert');
        }
        return $response;
    }

    /**
     * Send request to update accounts
     */
    protected function updateCampaigns()
    {
        $response = [];
        if (!is_null($this->updateCampaignIds)) {
            $response = $this->sendCampaignsRequest($this->updateCampaignIds, 'update');
        }
        return $response;
    }

    /**
     * @param $ruleId
     *
     * @throws \Exception
     */
    public function addRecord($ruleId)
    {
        $rule = $this->_ruleFactory->create()->load($ruleId);
        $id   = $this->checkExistedCampaign($rule);
        if (!$id) {
            $this->addToCreateQueue($rule);
        } else {
            $this->addToUpdateQueue($id['mObj'], $id['sid']);
        }
    }

    /**
     * @param $rule
     */
    protected function addToCreateQueue($rule)
    {
        $this->createCampaignIds[] = [
            'mObj' => $rule,
            'mid'  => $rule->getId()
        ];
    }

    /**
     * @param \Magento\CatalogRule\Model\Rule $rule
     * @param string $salesforceId
     */
    protected function addToUpdateQueue($rule, $salesforceId)
    {
        $this->updateCampaignIds[] = [
            'mObj' => $rule,
            'mid'  => $rule->getId(),
            'sid'  => $salesforceId
        ];
    }

    protected function unsetCreateQueue()
    {
        $this->createCampaignIds = null;
    }

    protected function unsetUpdateQueue()
    {
        $this->updateCampaignIds = null;
    }

    /**
     * @param $campaignIds
     * @param $operation
     *
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function sendCampaignsRequest($campaignIds, $operation)
    {
        $params = [];
        foreach ($campaignIds as $id) {
            $rule = $id['mObj'];
            $info = $this->_data->getCampaign($rule, $this->_type);
            $info = array_replace($info, ['Name' => $rule->getName()]);
            if (isset($id['sid'])) {
                $info += ['Id' => $id['sid']];
            }
            $params[] = $info;
        }
        $response = $this->_job->sendBatchRequest($operation, $this->_type, $this->_json->serialize($params));
        $this->saveReports($operation, $this->_type, $response, $campaignIds);
        return $response;
    }

    /**
     * @param $rule
     *
     * @return array|bool
     * @throws \Exception
     */
    protected function checkExistedCampaign($rule)
    {
        $existedCampaigns = $this->getAllSalesforceCampaigns();
        $ruleName         = trim($rule->getName());
        if (isset($existedCampaigns[$ruleName])) {
            unset($this->existedCampaigns[$ruleName]);
            return [
                'mObj' => $rule,
                'sid'  => $existedCampaigns[$ruleName]['Id']
            ];
        }
        return false;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getAllSalesforceCampaigns()
    {
        if (!is_null($this->existedCampaigns)) {
            return $this->existedCampaigns;
        }
        $allRules         = [];
        $existedCampaigns = $this->dataGetter->getAllSalesforceCampaigns();
        foreach ($existedCampaigns as $key => $value) {
            $allRules[$value['Name']] = $value;
        }
        $this->existedCampaigns = $allRules;
        return $this->existedCampaigns;
    }
}
