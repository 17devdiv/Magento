<?php

namespace Magenest\Salesforce\Model;

use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config as ResourceModelConfig;

/**
 * Class BulkConnector
 * @package Magenest\Salesforce\Model
 */
class BulkConnector
{
    const SALES_FORCE_API_VERSION = Connector::SALES_FORCE_API_VERSION;
    /**
     *#@+
     * Constants
     */
    const XML_PATH_SALESFORCE_EMAIL          = 'salesforcecrm/config/email';
    const XML_PATH_SALESFORCE_PASSWD         = 'salesforcecrm/config/passwd';
    const XML_PATH_SALESFORCE_SECURITY_TOKEN = 'salesforcecrm/config/security_token';
    const XML_PATH_SALESFORCE_INSTANCE_URL   = 'salesforcecrm/config/instance_url';

    /**
     * @var string
     */
    protected $instanceUrl;

    /**
     * @var \Magenest\Salesforce\Helper\Data
     */
    protected $helper;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var RequestLogFactory
     */
    protected $requestLogFactory;

    /**
     * BulkConnector constructor.
     * @param \Magenest\Salesforce\Helper\Data $helper
     * @param RequestLogFactory $requestLogFactory
     */
    public function __construct(
        \Magenest\Salesforce\Helper\Data $helper,
        RequestLogFactory $requestLogFactory
    )
    {
        $this->helper            = $helper;
        $this->requestLogFactory = $requestLogFactory;
    }

    /**
     * @throws \Exception
     */
    public function _getAccessToken()
    {
        $xml            = '<?xml version="1.0" encoding="utf-8" ?>';
        $xml            .= '<env:Envelope xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:env="http://schemas.xmlsoap.org/soap/envelope/">';
        $xml            .= '<env:Body>';
        $xml            .= '<n1:login xmlns:n1="urn:partner.soap.sforce.com">';
        $xml            .= '<n1:username>' . $this->helper->getEmail() . '</n1:username>';
        $xml            .= '<n1:password>' . $this->helper->getPassword() . $this->helper->getToken() . '</n1:password>';
        $xml            .= '</n1:login>';
        $xml            .= '</env:Body>';
        $xml            .= '</env:Envelope>';
        $xml            = trim($xml);
	    if ($this->helper->getConnectEnvironment() === 1 || $this->helper->getConnectEnvironment() === "1") {
		    $url            = 'https://login.salesforce.com/services/Soap/u/' . self::SALES_FORCE_API_VERSION;
	    } else {
		    $url            = 'https://test.salesforce.com/services/Soap/u/' . self::SALES_FORCE_API_VERSION;
	    }
        $headers        = [
            'Content-Type' => 'text/xml',
            'charset'      => 'UTF-8',
            'SOAPAction'   => 'login'
        ];
        $response       = $this->sendRequest($url, \Zend_Http_Client::POST, $headers, $xml);
        $parsedResponse = $this->parseXml($response);

        if ($sessionId = $parsedResponse->getElementsByTagName('sessionId')->item(0)) {
            $this->sessionId = $sessionId->textContent;
        } else {
            $exceptionMessage = '';
            if ($err = $parsedResponse->getElementsByTagName('faultstring')->item(0)) {
                $exceptionMessage = $err->textContent;
            }
            throw new \Exception('Cant get access token. ' . $exceptionMessage);
        }
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param string $params
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    protected function sendRequest($url, $method, $headers = [], $params = '')
    {
        $client = new \Zend_Http_Client($url);
        $client->setHeaders($headers);
        $client->setMethod($method);
        if ($method != \Zend_Http_Client::GET) {
            if (isset($headers['Content-Type'])) {
                $client->setRawData($params);
                $client->setEncType($headers['Content-Type']);
            } else {
                $client->setParameterPost($params);
            }
        }
        $response = $client->request()->getBody();
        $this->requestLogFactory->create()->addRequest(RequestLog::BULK_REQUEST_TYPE);
        return $response;
    }

    /**
     * @param $xml
     * @return \DOMDocument|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function parseXml($xml)
    {
        $parser = new \Magento\Framework\Xml\Parser();
        $parser->loadXML($xml);
        return $parser->getDom();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getAccessToken()
    {
        if (!$this->sessionId) {
            $this->_getAccessToken();
        }
        return $this->sessionId;
    }

    /**
     * @return string
     */
    protected function getInstanceUrl()
    {
        if ($this->instanceUrl === null) {
            $this->instanceUrl = $this->helper->getInstanceUrl();
        }
        return $this->instanceUrl;
    }
}
