<?php

namespace Magenest\Salesforce\Model\Sync;

use Magenest\Salesforce\Model\Connector;
use Magenest\Salesforce\Model\Data;
use Magenest\Salesforce\Model\QueueFactory;
use Magenest\Salesforce\Model\ReportFactory as ReportFactory;
use Magenest\Salesforce\Model\RequestLogFactory;
use Magento\Config\Model\ResourceModel\Config as ResourceModelConfig;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Sales\Model\Order as OrderModel;
use Magento\Sales\Model\OrderFactory;

/**
 * Class Opportunity
 * @package Magenest\Salesforce\Model\Sync
 */
class Opportunity extends Connector
{
    const SALESFORCE_OPPORTUNITY_ATTRIBUTE_CODE = 'salesforce_opportunity_id';

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Account
     */
    protected $_account;

    /**
     * @var Contact
     */
    protected $_contact;

    /**
     * @var Product
     */
    protected $_product;

    /** @var Order  */
    protected $_order;
    /**
     * @var Job
     */
    protected $_job;

    /** @var Data  */
    protected $_data;

    protected $existedOrders = null;

    protected $createOpportunityIds = null;

    protected $updateOpportunityIds = null;

    /** @var DataGetter  */
    protected $dataGetter;

    /**
     * Opportunity constructor.
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $json,
     * @param Serialize $serialize
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceModelConfig $resourceConfig
     * @param ReportFactory $reportFactory
     * @param QueueFactory $queueFactory
     * @param RequestLogFactory $requestLogFactory
     * @param Data $data
     * @param OrderFactory $orderFactory
     * @param Order $order
     * @param Account $account
     * @param Contact $contact
     * @param Product $product
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
        Data $data,
        OrderFactory $orderFactory,
        Order $order,
        Account $account,
        Contact $contact,
        Product $product,
        Job $job,
        DataGetter $dataGetter
    ){
        parent::__construct($json, $serialize, $scopeConfig, $resourceConfig, $reportFactory, $queueFactory, $requestLogFactory);
        $this->_orderFactory = $orderFactory;
        $this->_order        = $order;
        $this->_account      = $account;
        $this->_contact      = $contact;
        $this->_product      = $product;
        $this->_data         = $data;
        $this->_type         = 'Opportunity';
        $this->_table        = 'opportunity';
        $this->_job          = $job;
        $this->dataGetter    = $dataGetter;
    }

    /**
     * @param string $increment_id
     * @return mixed|string
     * @throws \Exception
     */
    public function sync($increment_id)
    {
        /** @var OrderModel $order */
        $order = $this->_orderFactory->create()->loadByIncrementId($increment_id);
        $date  = date('Y-m-d', strtotime($order->getCreatedAt()));

        $params = $this->_data->getOpportunity($order, $this->_type);

        $params  += [
            'CloseDate' => $date,
            'StageName' => 'Prospecting',
        ];
        $params  = array_replace($params, [
                'Name' => $order->getIncrementId()]
        );
        $existed = $this->checkExistedOpportunity($order);
        if (!$existed) {
            $opportunityId = $this->createRecords($this->_type, $params, $order->getIncrementId());
            $this->saveAttribute($order, $opportunityId);
            /**
             * Sync OpportunityLineItem
             */

            $params  = [];
            $itemIds = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $qty   = $item->getQtyOrdered();
                $price = $item->getPrice() - $item->getDiscountAmount() / $qty;

                if ($price > 0) {
                    $productId = $this->_product->sync($item->getProductId());
                    if ($productId && $opportunityId) {
                        $pricebookEntryId = $this->searchRecords('PricebookEntry', 'Product2Id', $productId);
                        $info             = [
                            'PricebookEntryId' => $pricebookEntryId,
                            'OpportunityId'    => $opportunityId,
                            'Quantity'         => $qty,
                            'UnitPrice'        => $price,
                        ];
                        $params[]         = $info;
                        $itemIds[]        = ['mid' => $item->getProductId()];
                    }
                }
            }
            if ($taxInfo = $this->_order->getTaxItemInfo($order, $opportunityId)) {
                $taxInfo['OpportunityId'] = $opportunityId;
                unset($taxInfo['OrderId']);
                $params[]  = $taxInfo;
                $itemIds[] = ['mid' => 'TAX'];
            }
            if ($shippingInfo = $this->_order->getShippingItemInfo($order, $opportunityId)) {
                $shippingInfo['OpportunityId'] = $opportunityId;
                unset($shippingInfo['OrderId']);
                $params[]  = $shippingInfo;
                $itemIds[] = ['mid' => 'SHIPPING'];
            }
            $response = $this->_job->sendBatchRequest('insert', 'OpportunityLineItem', $this->_json->serialize($params));
            $this->saveReports('create', 'OpportunityLineItem', $response, $itemIds);
        } else {
            $opportunityId = $existed['sid'];
            $this->updateRecords($this->_type, $opportunityId, $params, $order->getIncrementId());
        }

        return $opportunityId;
    }

    /**
     * @return array|mixed|string|null
     */
    public function syncAllOpportunities()
    {
        try {
            $orders      = $this->_orderFactory->create()->getCollection();
            $lastOrderId = $orders->getLastItem()->getId();
            $count       = 0;
            $response    = [];
            /** @var OrderModel $order */
            foreach ($orders as $order) {
                $this->addRecord($order->getIncrementId());
                $count++;
                if ($count >= 10000 || $order->getId() == $lastOrderId) {
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
     * @param string $orderIncrementId
     */
    public function addRecord($orderIncrementId)
    {
        $order = $this->_orderFactory->create()->loadByIncrementId($orderIncrementId);
        $id    = $this->checkExistedOpportunity($order);
        if ($order->getIncrementId() && !$id) {
            $this->addToCreateOpportunityQueue($order);
        } else {
            $this->addToUpdateOpportunityQueue($id['mObj'], $id['sid']);
        }
    }

    /**
     * @param OrderModel $order
     *
     * @return array|bool
     */
    public function checkExistedOpportunity($order)
    {
        $existedOrders    = $this->getAllSalesforceOpportunity();
        $orderIncrementId = $order->getIncrementId();
        if (isset($existedOrders[$orderIncrementId])) {
            unset($this->existedOrders[$orderIncrementId]);
            return [
                'mObj' => $order,
                'sid'  => $existedOrders[$orderIncrementId]['Id']
            ];
        }
        return false;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getAllSalesforceOpportunity()
    {
        if (!is_null($this->existedOrders)) {
            return $this->existedOrders;
        }
        $existedOrders = $this->dataGetter->getAllSalesforceOpportunities();
        $allOrders     = [];
        foreach ($existedOrders as $key => $value) {
            $allOrders[$value['Name']] = $value;
        }
        $this->existedOrders = $allOrders;
        return $this->existedOrders;
    }

    /**
     * @return array|mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function syncQueue()
    {
        $createOpportunityResponse = $this->createOpportunities();
        $this->saveAttributes($this->createOpportunityIds, $createOpportunityResponse);
        $createOpportunityLineItemResponse = $this->createOpportunityLineItem();
        $updateOpportunityResponse         = $this->updateOpportunities();
        $this->saveAttributes($this->updateOpportunityIds, $updateOpportunityResponse);

        $response = $createOpportunityResponse + $createOpportunityLineItemResponse + $updateOpportunityResponse;

        $this->unsetCreateOpportunityQueue();
        $this->unsetUpdateOpportunityQueue();
        return $response;
    }

    /**
     * @param OrderModel $order
     */
    protected function addToCreateOpportunityQueue($order)
    {
        $this->createOpportunityIds[] = [
            'mid'  => $order->getIncrementId(),
            'mObj' => $order
        ];
    }

    protected function unsetCreateOpportunityQueue()
    {
        $this->createOpportunityIds = null;
    }

    /**
     * @param OrderModel $order
     * @param $salesforceId
     */
    protected function addToUpdateOpportunityQueue($order, $salesforceId)
    {
        $this->updateOpportunityIds[] = [
            'mObj' => $order,
            'mid'  => $order->getIncrementId(),
            'sid'  => $salesforceId
        ];
    }

    protected function unsetUpdateOpportunityQueue()
    {
        $this->updateOpportunityIds = null;
    }

    /**
     * @return array|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createOpportunities()
    {
        $response = [];
        if (!is_null($this->createOpportunityIds)) {
            $response = $this->sendOpportunitiesRequest($this->createOpportunityIds, 'insert');
        }
        return $response;
    }

    /**
     * @return array|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function updateOpportunities()
    {
        $response = [];
        if (!is_null($this->updateOpportunityIds)) {
            $response = $this->sendOpportunitiesRequest($this->updateOpportunityIds, 'update');
        }
        return $response;
    }

    /**
     * @return array|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    protected function createOpportunityLineItem()
    {
        if (is_null($this->createOpportunityIds)) {
            return [];
        }
        $params   = [];
        $itemIds  = [];
        $orderIds = $this->createOpportunityIds;

        /** @var OrderModel $order */
        foreach ($orderIds as $orderId) {
            $order         = $orderId['mObj'];
            $opportunityId = $order->getData(self::SALESFORCE_OPPORTUNITY_ATTRIBUTE_CODE);
            foreach ($order->getAllVisibleItems() as $item) {
                $qty   = $item->getQtyOrdered();
                $price = $item->getPrice() - $item->getDiscountAmount() / $qty;

                if ($price > 0) {
                    $productId = $item->getProduct()->getData(Product::SALESFORCE_PRODUCT_ATTRIBUTE_CODE);
                    if (!$productId) {
                        $productId = $this->_product->sync($item->getProductId());
                    }
                    if ($productId && $opportunityId) {
                        $pricebookEntryId = $this->searchRecords('PricebookEntry', 'Product2Id', $productId);
                        if (!$pricebookEntryId) {
                            $productId        = $this->_product->sync($item->getProductId());
                            $pricebookEntryId = $this->searchRecords('PricebookEntry', 'Product2Id', $productId);
                        }
                        $info      = [
                            'PricebookEntryId' => $pricebookEntryId,
                            'OpportunityId'    => $opportunityId,
                            'Quantity'         => $qty,
                            'UnitPrice'        => $price,
                        ];
                        $params[]  = $info;
                        $itemIds[] = ['mid' => $item->getProductId()];
                    }
                }
            }
            if ($taxInfo = $this->_order->getTaxItemInfo($order, $opportunityId)) {
                $taxInfo['OpportunityId'] = $opportunityId;
                unset($taxInfo['OrderId']);
                $params[]  = $taxInfo;
                $itemIds[] = ['mid' => 'TAX'];
            }
            if ($shippingInfo = $this->_order->getShippingItemInfo($order, $opportunityId)) {
                $shippingInfo['OpportunityId'] = $opportunityId;
                unset($shippingInfo['OrderId']);
                $params[]  = $shippingInfo;
                $itemIds[] = ['mid' => 'SHIPPING'];
            }
        }
        $response = $this->_job->sendBatchRequest('insert', 'OpportunityLineItem', $this->_json->serialize($params));
        $this->saveReports('create', 'OpportunityLineItem', $response, $itemIds);
        return $response;
    }

    /**
     * @param $opportunityIds
     * @param $operation
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function sendOpportunitiesRequest($opportunityIds, $operation)
    {
        $params = [];
        foreach ($opportunityIds as $orderId) {
            $order = $orderId['mObj'];
            $date  = date('Y-m-d', strtotime($order->getCreatedAt()));
            $info  = $this->_data->getOpportunity($order, $this->_type);
            $info  += [
                'CloseDate' => $date,
                'StageName' => 'Prospecting',
            ];
            $info  = array_replace($info, [
                'Name' => $order->getIncrementId()
            ]);
            if (isset($orderId['sid'])) {
                $info += ['Id' => $orderId['sid']];
            }
            $params[] = $info;
        }
        $response = $this->_job->sendBatchRequest($operation, $this->_type, $this->_json->serialize($params));
        $this->saveReports($operation, $this->_type, $response, $opportunityIds);
        return $response;
    }

    /**
     * @param $orderIds
     * @param $response
     * @throws \Exception
     */
    protected function saveAttributes($orderIds, $response)
    {
        if (empty($orderIds) || is_null($orderIds)) {
            return;
        }
        if (is_array($response) && is_array($orderIds)) {
            $total = count($response);
            for ($i = 0; $i < $total; $i++) {
                if (isset($orderIds[$i])) {
                    $order = $orderIds[$i]['mObj'];
                    if (isset($response[$i]['id']) && $order->getId()) {
                        $this->saveAttribute($order, $response[$i]['id']);
                    }
                }
            }
        } else {
            throw new \Exception('Response not an array');
        }
    }

    /**
     * @param OrderModel $order
     * @param $salesforceId
     *
     * @throws \Exception
     */
    protected function saveAttribute($order, $salesforceId)
    {
        $resource = $order->getResource();
        $order->setData(self::SALESFORCE_OPPORTUNITY_ATTRIBUTE_CODE, $salesforceId);
        $resource->saveAttribute($order, self::SALESFORCE_OPPORTUNITY_ATTRIBUTE_CODE);
    }
}
