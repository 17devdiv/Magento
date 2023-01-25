<?php

namespace Magenest\Salesforce\Model;

use Magento\Config\Model\ResourceModel\Config as ResourceModelConfig;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Connector
 * @package Magenest\Salesforce\Model
 */
class Connector
{
    const SALES_FORCE_API_VERSION = '45.0';
    /**
     *#@+
     * Constants
     */
    const XML_PATH_SALESFORCE_IS_CONNECTED        = 'salesforcecrm/config/is_connected';
    const XML_PATH_SALESFORCE_EMAIL               = 'salesforcecrm/config/email';
    const XML_PATH_SALESFORCE_PASSWD              = 'salesforcecrm/config/passwd';
    const XML_PATH_SALESFORCE_CLIENT_ID           = 'salesforcecrm/config/client_id';
    const XML_PATH_SALESFORCE_CLIENT_SECRET       = 'salesforcecrm/config/client_secret';
    const XML_PATH_SALESFORCE_SECURITY_TOKEN      = 'salesforcecrm/config/security_token';
    const XML_PATH_SALESFORCE_CONNECT_ENVIRONMENT = 'salesforcecrm/config/connect_environment';
    const XML_PATH_SALESFORCE_ACCESS_TOKEN        = 'salesforcecrm/config/access_token';
    const XML_PATH_SALESFORCE_INSTANCE_URL        = 'salesforcecrm/config/instance_url';
    const XML_PATH_SALESFORCE_CONTACT_ENABLE      = 'salesforcecrm/sync/contact';
    const XML_PATH_SALESFORCE_ACCOUNT_ENABLE      = 'salesforcecrm/sync/account';
    const XML_PATH_SALESFORCE_LEAD_ENABLE         = 'salesforcecrm/sync/lead';
    const XML_PATH_SALESFORCE_CAMPAIGN_ENABLE     = 'salesforcecrm/sync/campaign';
    const XML_PATH_SALESFORCE_ORDER_ENABLE        = 'salesforcecrm/sync/order';
    const XML_PATH_SALESFORCE_PRODUCT_ENABLE      = 'salesforcecrm/sync/product';

    const IGNORE_FIELD_TYPES = array('picklist', 'reference', 'calculated', 'multipicklist');// array('date', 'datetime', 'address')

    /**
     * @var string serialize string
     */
    protected $fields;

    /**
     * @var string serialize string
     */
    protected $fieldsType = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     *
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * @var \Magenest\Salesforce\Model\ReportFactory
     */
    protected $_reportFactory;

    /**
     * @var string
     */
    protected $_type;

    /**
     * @var string
     */
    protected $_table;

    /**
     * @var QueueFactory
     */
    protected $_queueFactory;

    /** @var \Magento\Framework\Serialize\Serializer\Serialize  */
    protected $_serializer;

    /**
     * @var QueueFactory
     */
    protected $_requestLogFactory;

    protected $credential = null;

    protected $requestsSent = 0;

    /** @var \Magento\Framework\Serialize\Serializer\Json  */
    protected $_json;

    /**
     * Connector constructor.
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceModelConfig $resourceConfig
     * @param ReportFactory $reportFactory
     * @param QueueFactory $queueFactory
     * @param RequestLogFactory $requestLogFactory
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        ScopeConfigInterface $scopeConfig,
        ResourceModelConfig $resourceConfig,
        ReportFactory $reportFactory,
        QueueFactory $queueFactory,
        RequestLogFactory $requestLogFactory
    ) {
        $this->_json = $json;
        $this->_serializer = $serialize;
        $this->_scopeConfig       = $scopeConfig;
        $this->_resourceConfig    = $resourceConfig;
        $this->_reportFactory     = $reportFactory;
        $this->_queueFactory      = $queueFactory;
        $this->_requestLogFactory = $requestLogFactory;
    }

    /**
     * @param $data
     */
    public function setCredential($data)
    {
        $this->credential = $data;
    }

    /**
     * Get Access Token & Instance Url
     *
     * @param array $data
     * @param bool $update
     * @return mixed|string
     * @throws \Zend_Http_Client_Exception
     */
    public function getAccessToken($data = [], $update = false)
    {
        try {
            if ((!empty($data) && $update)) {
                $username            = $data['username'];
                $password            = $data['password'];
                $client_id           = $data['client_id'];
                $client_secret       = $data['client_secret'];
                $security_token      = $data['security_token'];
                $connect_environment = $data['connect_environment'];
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_EMAIL, $data['username'], 'default', 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_PASSWD, $data['password'], 'default', 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_CLIENT_ID, $data['client_id'], 'default', 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_CLIENT_SECRET, $data['client_secret'], 'default', 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_SECURITY_TOKEN, $data['security_token'], 'default', 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_CONNECT_ENVIRONMENT, $data['connect_environment'], 'default', 0);
            } elseif (!is_null($this->credential)) {
                $data                = $this->credential;
                $username            = $data['username'];
                $password            = $data['password'];
                $client_id           = $data['client_id'];
                $client_secret       = $data['client_secret'];
                $security_token      = $data['security_token'];
                $connect_environment = $data['connect_environment'];
            } else {
                $username            = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_EMAIL);
                $password            = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_PASSWD);
                $client_id           = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_CLIENT_ID);
                $client_secret       = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_CLIENT_SECRET);
                $security_token      = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_SECURITY_TOKEN);
                $connect_environment = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_CONNECT_ENVIRONMENT);
            }

            if (!$username || !$password || !$client_id || !$client_secret || !$security_token) {
                throw new \InvalidArgumentException('Field not setup !');
            }

            if ($connect_environment === 1 || $connect_environment === "1") {
                $base_url = 'https://login.salesforce.com/';
            } else {
                $base_url = 'https://test.salesforce.com/';
            }
            $url      = $base_url . 'services/oauth2/token';
            $params   = [
                'grant_type'    => 'password',
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'username'      => $username,
                'password'      => $password . $security_token
            ];
            $response = $this->makeRequest(\Zend_Http_Client::POST, $url, [], $params);
            $response = $this->_json->unserialize($response, true);
            if (isset($response['access_token']) && isset($response['instance_url'])) {
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_INSTANCE_URL, $response['instance_url'], 'default', 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_ACCESS_TOKEN, $response['access_token'], 'default', 0);
                $this->_resourceConfig->saveConfig(self::XML_PATH_SALESFORCE_IS_CONNECTED, 1, 'default', 0);
                unset($response['id']);
                unset($response['token_type']);
                unset($response['signature']);
                unset($response['issued_at']);

                return $response;
            } else {
                throw new \InvalidArgumentException($response['error_description']);
            }
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * @param $method
     * @param $path
     * @param null $paramter
     * @param bool $useFreshCredential
     * @return mixed|string
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function sendRequest($method, $path, $paramter = null, $useFreshCredential = false)
    {
        if ($useFreshCredential) {
            $instance_url = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_INSTANCE_URL, ScopeInterface::SCOPE_STORE);
            $access_token = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_ACCESS_TOKEN, ScopeInterface::SCOPE_STORE);
        }

        try {
            if (!isset($instance_url) || !isset($access_token) || $useFreshCredential) {
                $login        = $this->getAccessToken();
                $instance_url = $login['instance_url'];
                $access_token = $login['access_token'];
            }
        } catch (\InvalidArgumentException $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }

        $headers = ["Content-Type" => "application/json"];
        if (isset($access_token)) {
            $headers = array_merge($headers, ["Authorization" => "Bearer " . $access_token]);
        }
        $url      = $instance_url . $path;
        $response = $this->makeRequest($method, $url, $headers, $paramter);
        if(!empty($response)){
            $response = $this->_json->unserialize($response, true);
        }
        if (isset($response[0]['errorCode']) && $response[0]['errorCode'] == 'INVALID_SESSION_ID') {
            $this->sendRequest($method, $path, $paramter, true);
        }

        return $response;
    }

    /**
     * @param $table
     * @param $parameter
     * @param null $mid
     * @return bool|mixed
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function createRecords($table, $parameter, $mid = null)
    {
        $path     = "/services/data/v" . self::SALES_FORCE_API_VERSION . "/sobjects/" . $table . "/";
        $response = $this->sendRequest(\Zend_Http_Client::POST, $path, $parameter);
        if (isset($response["id"])) {
            $id = $response["id"];
            $this->saveReport($id, 'create', $table, 1, null, $mid);
            return $id;
        } else if (isset($response[0]['errorCode'])) {
            $message = $response[0]['errorCode'] . ": " . $response[0]['message'];
            $this->saveReport(null, null, $this->_type, 2, $message, $mid);
            throw new \Exception($message);
        }

        return false;
    }

    /**
     * Delete a record in salesforce
     *
     * @param $table
     * @param $id
     * @param null $mid
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function deleteRecords($table, $id, $mid = null)
    {
        $path = "/services/data/v" . self::SALES_FORCE_API_VERSION . "/sobjects/" . $table . "/" . $id;
        $this->sendRequest(\Zend_Http_Client::DELETE, $path);
        $this->saveReport($id, 'delete', $table, 1, null, $mid);
    }

    /**
     * @param $table
     * @param $id
     * @param $paramter
     * @param null $mid
     *
     * @return mixed
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function updateRecords($table, $id, $paramter, $mid = null)
    {
        $path = "/services/data/v" . self::SALES_FORCE_API_VERSION . "/sobjects/" . $table . "/" . $id;
        $this->sendRequest(\Zend_Http_Client::PATCH, $path, $paramter);
        $this->saveReport($id, 'update', $table, 1, null, $mid);
        return $id;
    }

    /**
     * @param $table
     * @param $field
     * @param $value
     * @return bool|mixed
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function searchRecords($table, $field, $value)
    {

        $query = "SELECT Id FROM $table WHERE $field = '$value' ";
        if ($table == 'PricebookEntry') {
            $query .= ' ORDER BY Id ';
        }

        $query .= 'LIMIT 1';
        $path  = '/services/data/v' . self::SALES_FORCE_API_VERSION . '/query?q=' . urlencode($query);

        $response = $this->sendRequest(\Zend_Http_Client::GET, $path);
        if (isset($response['totalSize']) && $response['totalSize'] == 1) {
            $id = $response['records']['0']['Id'];
            return $id;
        }

        return false;
    }

    /**
     * @param $table
     * @return string
     * @throws LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function getFields($table)
    {
        if ($this->fields === null) {
            $path             = '/services/data/v' . self::SALES_FORCE_API_VERSION . '/sobjects/' . $table . '/describe/';
            $response         = $this->sendRequest(\Zend_Http_Client::GET, $path);
            $data             = [];
            $type             = [];
            $ignoreFieldTypes = $this->getIgnoreFieldTypes();
            if (isset($response['fields'])) {
                foreach ($response['fields'] as $index => $field) {
                    if ($field['createable'] && $field['updateable'] && !in_array($field['type'], $ignoreFieldTypes)) {
                        $label       = $field['label'] . " (".$field['type'].")";
                        $name        = $field['name'];
                        $data[$name] = $label;
                        $type[$name] = $field['type'];
                    }
                }
            }
            $this->fields = $this->_serializer->serialize($data);
            $this->fieldsType = $this->_serializer->serialize($type);
        }
        $results = $this->_json->serialize([
            'fields' => $this->fields,
            'types' => $this->fieldsType
        ]);
        return $results;
    }

    /**
     * @return array
     */
    public function getIgnoreFieldTypes()
    {
        return self::IGNORE_FIELD_TYPES;
    }

    /**
     * @param $id
     * @param $action
     * @param $table
     * @param int $status
     * @param null $message
     * @param null $mid
     */
    public function saveReport($id, $action, $table, $status = 1, $message = null, $mid = null)
    {
        $model = $this->_reportFactory->create();
        $model->saveReport($id, $action, $table, $status, $message, $mid);

        return;
    }

    /**
     * @param string $action
     * @param string $table
     * @param array $response
     * @param array $magentoIds
     */
    public function saveReports($action, $table, $response, $magentoIds)
    {
        if (is_array($response)) {
            $total   = count($response);
            $reports = [];
            for ($i = 0; $i < $total; $i++) {
                $recordId  = null;
                $status    = 2;
                $message   = null;
                $magentoId = null;
                if (isset($response[$i]['success']) && $response[$i]['success']) {
                    $recordId  = $response[$i]['id'];
                    $status    = 1;
                    $magentoId = isset($magentoIds[$i]['mid']) ? $magentoIds[$i]['mid'] : null;
                } elseif (isset($response[$i]['errors'][0])) {
                    $message = 'ERROR ';
                    foreach ($response[$i]['errors'] as $error) {
                        $message .= $error['message'] . ';';
                    }
                } else {
                    $message = $this->_serializer->serialize($response);
                }
                $params    = [
                    'record_id'        => $recordId,
                    'action'           => $action,
                    'salesforce_table' => $table,
                    'status'           => $status,
                    'msg'              => $message,
                    'magento_id'       => $magentoId
                ];
                $params    += $this->_reportFactory->create()->getInfoReport();
                $reports[] = $params;
            }
            $this->_reportFactory->create()->saveReports($reports);
        }
    }

    /**
     * @param $method
     * @param $url
     * @param array $headers
     * @param array $params
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    public function makeRequest($method, $url, $headers = [], $params = [])
    {
        $client = new \Zend_Http_Client($url);
        $client->setHeaders($headers);
        if ($method != \Zend_Http_Client::GET) {
            $client->setParameterPost($params);
            if (isset($headers['Content-Type']) && $headers['Content-Type'] == 'application/json') {
                $client->setEncType('application/json');
                $params = $this->_json->serialize($params);
                $client->setRawData($params);
            }
        }
        $response = $client->request($method)->getBody();
        $this->_requestLogFactory->create()->addRequest(RequestLog::REST_REQUEST_TYPE);
        return $response;
    }

    /**
     * @return array|null
     */
    public function syncAllQueue()
    {
        $type = $this->_type;
        if ($type == 'Product2') {
            $type = 'Products';
        }
        $queueModel      = $this->_queueFactory->create();
        $queueCollection = $queueModel->getCollection()
            ->addFieldToFilter('type', rtrim($type, 's'));

        $lastId    = (int)$queueCollection->getLastItem()->getId();
        $count     = 0;
        $response  = [];
        $maxRecord = 5000;
        /** @var \Magenest\Salesforce\Model\Queue $queue */
        foreach ($queueCollection as $queue) {
            $entityId = $queue->getEntityId();
            $this->addRecord($entityId);
            $count++;
            if ($count >= $maxRecord || $queue->getId() == $lastId) {
                $response += $this->syncQueue();
                $count    = 0;
            }
        }
        $count              = 0;
        $queueCollectionArr = [];
        foreach ($queueCollection as $queue) {
            $queueCollectionArr[] = $queue->getId();
            $count++;
            if ($count >= $maxRecord || $queue->getId() == $lastId) {
                $queueModel->deleteMultiQueues($queueCollectionArr);
                $count              = 0;
                $queueCollectionArr = [];
            }
        }
        return $response;
    }

    public function addRecord($entityId)
    {
    }

    public function syncQueue()
    {
        return null;
    }
}
