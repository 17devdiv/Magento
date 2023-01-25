<?php

namespace Magenest\Salesforce\Model\Sync;

use Magenest\Salesforce\Model\BulkConnector;

/**
 * Class Job
 * @package Magenest\Salesforce\Model\Sync
 */
class Job extends BulkConnector
{
	const BATCH_ID_MAXIMUM_RETRIEVAL_NUMBER = 100;

    /**
     * @var string
     */
    protected $jobId;

    /**
     * @var string
     */
    protected $batchId;
    /**
     * @var int
     */
    protected $batchRequestSent = 0;

    /**
     * @param string $operation
     * @param string $object
     * @param string $batch
     * @param string $contentType
     * @return mixed|string
     * @throws \Exception
     */
    public function sendBatchRequest($operation = '', $object = '', $batch = '', $contentType = 'JSON')
    {
        if ($batch == '[]') {
            return [];
        }
        $batchResultId = '';
        $this->getAccessToken();
        $this->createJob($operation, $object, $contentType);
        $this->addBatch($batch);
        if ($operation == 'query') {
            $batchResultId = $this->getBatchResultId();
        }
        $queryResult = $this->getBatchResult($batchResultId);
        $this->closeJob();
        return $queryResult;
    }

    /**
     * @param string $operation
     * @param string $object
     * @param string $contentType
     * @throws \Zend_Http_Client_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    protected function createJob($operation = '', $object = '', $contentType = 'JSON')
    {
        $xml            = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml            .= '<jobInfo xmlns="http://www.force.com/2009/06/asyncapi/dataload">';
        $xml            .= '<operation>' . $operation . '</operation>';
        $xml            .= '<object>' . $object . '</object>';
        $xml            .= '<contentType>' . $contentType . '</contentType>';
        $xml            .= '</jobInfo>';
        $xml            = trim($xml);
        $url            = $this->getInstanceUrl() . '/services/async/' . self::SALES_FORCE_API_VERSION . '/job';
        $headers        = [
            'Content-Type'   => 'text/xml',
//            'charset'        => 'UTF-8',
            'X-SFDC-Session' => $this->sessionId
        ];
        $response       = $this->sendRequest($url, \Zend_Http_Client::POST, $headers, $xml);
        $parsedResponse = $this->parseXml($response);
        if (!$this->jobId = $parsedResponse->getElementsByTagName('id')[0]->nodeValue) {
            throw new \Exception('Cant create Job: ' . $response);
        }
    }

    /**
     * @param string $batch
     * @throws \Zend_Http_Client_Exception
     * @throws \Exception
     */
    protected function addBatch($batch = '')
    {
        $url            = $this->getInstanceUrl() . '/services/async/' . self::SALES_FORCE_API_VERSION . '/job/' . $this->jobId . '/batch/';
        $headers        = [
            'Content-Type'   => 'application/json',
//            'charset'        => 'UTF-8',
            'X-SFDC-Session' => $this->sessionId
        ];
        $response       = $this->sendRequest($url, \Zend_Http_Client::POST, $headers, $batch);
        $parsedResponse = json_decode($response, true);
        if (!$this->batchId = $parsedResponse['id']) {
            throw new \Exception('Cant add batch to Job: ' . $response);
        }
    }

    /**
     * @throws \Zend_Http_Client_Exception
     */
    protected function closeJob()
    {
        $xml     = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml     .= '<jobInfo xmlns="http://www.force.com/2009/06/asyncapi/dataload">';
        $xml     .= '<state>Closed</state>';
        $xml     .= '</jobInfo>';
        $xml     = trim($xml);
        $url     = $this->getInstanceUrl() . '/services/async/' . self::SALES_FORCE_API_VERSION . '/job/' . $this->jobId;
        $headers = [
            'Content-Type'   => 'text/xml',
//            'charset'        => 'UTF-8',
            'X-SFDC-Session' => $this->sessionId
        ];
        $this->sendRequest($url, \Zend_Http_Client::POST, $headers, $xml);
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getBatchResultId()
    {
        $url      = $this->getInstanceUrl() . '/services/async/' . self::SALES_FORCE_API_VERSION . '/job/' . $this->jobId . '/batch/' . $this->batchId . '/result/';
        $headers  = [
            'Content-Type'   => 'application/json',
//            'charset'        => 'UTF-8',
            'X-SFDC-Session' => $this->sessionId
        ];
        $response = $this->sendRequest($url, \Zend_Http_Client::GET, $headers);
        $this->handleBatchRequestSent($response);
        $parsedResponse = json_decode($response, true);
        if (isset($parsedResponse[0]['id'])) {
            return $parsedResponse[0]['id'];
        } elseif (isset($parsedResponse[0])) {
            return $parsedResponse[0];
        } else {
            $response       = $this->getBatchStatus();
            $parsedResponse = json_decode($response, true);
            if (!empty($parsedResponse['id']) && !empty($parsedResponse['jobId'])) {
                return $this->getBatchResultId();
            } else {
                throw new \Exception('Cant get Batch Result Id:  ' . $response . '. Please contact Administrator for more information.');
            }
        }
    }

    /**
     * @param $response
     * @throws \Exception
     */
    private function handleBatchRequestSent($response)
    {
        if (++$this->batchRequestSent > self::BATCH_ID_MAXIMUM_RETRIEVAL_NUMBER) {
            throw new \Exception('Cant get Batch Result Id:  ' . $response . '. Please contact Administrator for more information.');
        }
    }

    /**
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    protected function getBatchStatus()
    {
        $url      = $this->getInstanceUrl() . '/services/async/' . self::SALES_FORCE_API_VERSION . '/job/' . $this->jobId . '/batch/' . $this->batchId;
        $headers  = [
            'Content-Type'   => 'application/json',
//            'charset'        => 'UTF-8',
            'X-SFDC-Session' => $this->sessionId
        ];
        $response = $this->sendRequest($url, \Zend_Http_Client::GET, $headers);
        return $response;
    }

    /**
     * @param string $resultId
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    protected function getBatchResult($resultId = '')
    {
        $count = 0;
        do {
            $url             = $this->getInstanceUrl() . '/services/async/' . self::SALES_FORCE_API_VERSION . '/job/' . $this->jobId . '/batch/' . $this->batchId . '/result/' . $resultId;
            $headers         = [
                'Content-Type'   => 'application/json',
//                'charset'        => 'UTF-8',
                'X-SFDC-Session' => $this->sessionId
            ];
            $response        = $this->sendRequest($url, \Zend_Http_Client::GET, $headers);
            $decodedResponse = json_decode($response, true);
        } while ((isset($decodedResponse['exceptionMessage']) && $decodedResponse['exceptionMessage'] == 'Batch not completed') || (++$count > self::BATCH_ID_MAXIMUM_RETRIEVAL_NUMBER));
        return $decodedResponse;
    }
}
