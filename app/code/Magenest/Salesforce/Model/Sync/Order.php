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

namespace Magenest\Salesforce\Model\Sync;

use Magenest\Salesforce\Model\Connector;
use Magenest\Salesforce\Model\Data;
use Magenest\Salesforce\Model\QueueFactory;
use Magenest\Salesforce\Model\ReportFactory as ReportFactory;
use Magenest\Salesforce\Model\RequestLogFactory;
use Magento\Config\Model\ResourceModel\Config as ResourceModelConfig;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Sales\Model\OrderFactory;

/**
 * Class Order
 * @package Magenest\Salesforce\Model\Sync
 */
class Order extends Connector
{
    const SALESFORCE_ORDER_ATTRIBUTE_CODE = 'salesforce_order_id';

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customerFactory;

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

    /**
     * @var Job
     */
    protected $_job;

    /**
     * @var Data
     */
    protected $_data;

    protected $existedOrders = null;

    protected $createOrderIds = null;

    protected $updateOrderIds = null;

    /** @var DataGetter  */
    protected $dataGetter;

    protected $_standardPriceBookId = null;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_category;

    /**
     * Order constructor.
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param Serialize $serialize
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceModelConfig $resourceConfig
     * @param ReportFactory $reportFactory
     * @param QueueFactory $queueFactory
     * @param RequestLogFactory $requestLogFactory
     * @param OrderFactory $orderFactory
     * @param CustomerFactory $customer
     * @param Account $account
     * @param Contact $contact
     * @param Product $product
     * @param Data $data
     * @param Job $job
     * @param DataGetter $dataGetter
     * @param \Magento\Catalog\Model\CategoryFactory $category
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        ScopeConfigInterface $scopeConfig,
        ResourceModelConfig $resourceConfig,
        ReportFactory $reportFactory,
        QueueFactory $queueFactory,
        RequestLogFactory $requestLogFactory,
        OrderFactory $orderFactory,
        CustomerFactory $customer,
        Account $account,
        Contact $contact,
        Product $product,
        Data $data,
        Job $job,
        DataGetter $dataGetter,
        \Magento\Catalog\Model\CategoryFactory $category
    ){
        parent::__construct($json, $serialize, $scopeConfig, $resourceConfig, $reportFactory, $queueFactory, $requestLogFactory);
        $this->_orderFactory    = $orderFactory;
        $this->_customerFactory = $customer;
        $this->_account         = $account;
        $this->_contact         = $contact;
        $this->_product         = $product;
        $this->_data            = $data;
        $this->_type            = 'Order';
        $this->_table           = 'order';
        $this->_job             = $job;
        $this->dataGetter       = $dataGetter;
        $this->_category        = $category;
    }

    /**
     * @param $d
     *
     * @return array|bool|string
     */
    public function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->utf8ize($v);
            }
        } elseif (is_string($d)) {
            return utf8_encode($d);
        }
        return $d;
    }

    /**
     * @param $increment_id
     * @return string
     * @throws \Exception
     */
    public function sync($increment_id)
    {
        $order      = $this->_orderFactory->create()->loadByIncrementId($increment_id);
        $customerId = $order->getCustomerId();
        $date       = date('Y-m-d', strtotime($order->getCreatedAt()));
        $email      = $order->getCustomerEmail();
        $params     = $this->_data->getOrder($order, $this->_type);
        // Get pricebookId of "Standard Price Book"
        $pricebookId = $this->_getStandardPriceBookId();
        /*
            * Require Field:
            *
            * 1. AccountId
            * 2. EffectiveDate
            * 3. Status
            * 4. PriceBook2Id
         */
        $params += [
            'EffectiveDate' => $date,
            'Status'        => 'Draft',
            'Pricebook2Id'  => $pricebookId,
        ];
        $sid    = $order->getData(self::SALESFORCE_ORDER_ATTRIBUTE_CODE);
        if ($sid) {
            $paramsUTF8 = $this->utf8ize($params);
            $orderId    = $sid;
            $orderId    = $this->updateRecords($this->_type, $orderId, $paramsUTF8, $order->getIncrementId());
            $this->saveAttribute($order, $orderId);
        } else {
            /*
             * 1. Get accountId, create new if not exist
             * 2. Create new Contacts if not exist
            */
            if ($customerId) {
                $accountId = $this->_account->sync($customerId);
                $this->_contact->sync($customerId);
            } else {
                $accountId = $this->_account->syncByEmail($email);
                $data      = [
                    'Email' => $email
                ];
                /**
                 * Required field
                 *
                 * If not be a account, LastName will be Guest
                 */
                $data += [
                    'LastName' => 'Guest'
                ];
                $this->_contact->syncByEmail($data);
            }
            /*
             * Number identifying the purchase order:
             * PoNumber
            */
            $params     += [
                'AccountId' => $accountId,
                //                'PoNumber'  => $increment_id,
            ];
            $paramsUTF8 = $this->utf8ize($params);
            // Create new Order
            $orderId = $this->createRecords($this->_type, $paramsUTF8, $order->getIncrementId());
            $this->saveAttribute($order, $orderId);
            /*
                * Add new record to OrderItem need:
                *
                * 1. productId
                * 2. pricebookEntryId       *
            */
            foreach ($order->getAllVisibleItems() as $item) {
                $product_id = $item->getProductId();
                $qty        = $item->getQtyOrdered();
                $price      = $item->getPrice() - $item->getDiscountAmount() / $qty;
                if ($price > 0) {
                    // 5. Get productId
                    $productId = $this->_product->sync($product_id);

                    $pricebookEntryId = $this->searchRecords('PricebookEntry', 'Product2Id', $productId);
                    $output           = [
                        'PricebookEntryId' => $pricebookEntryId,
                        'OrderId'          => $orderId,
                        'Quantity'         => $qty,
                        'UnitPrice'        => $price,
                    ];

                    // 6. Add Record to OrderItem table
                    $this->createRecords('OrderItem', $output, $product_id);
                }
            }//end foreach

            if ($taxInfo = $this->getTaxItemInfo($order, $orderId)) {
                $this->createRecords('OrderItem', $taxInfo, 'TAX');
            }

            if ($shippingInfo = $this->getShippingItemInfo($order, $orderId)) {
                $this->createRecords('OrderItem', $shippingInfo, 'SHIPPING');
            }
        }
        return $orderId;
    }

    /**
     * @return array|mixed|string|null
     */
    public function syncAllOrders()
    {
        try {
            $orders      = $this->_orderFactory->create()->getCollection();
            $lastOrderId = $orders->getLastItem()->getId();
            $count       = 0;
            $response    = [];
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
        $orderData = $order->getData();
        $ids = false;
        if(!empty($orderData) && isset($orderData[\Magenest\Salesforce\Model\Sync\Order::SALESFORCE_ORDER_ATTRIBUTE_CODE])){
            $salesforceId = $orderData[\Magenest\Salesforce\Model\Sync\Order::SALESFORCE_ORDER_ATTRIBUTE_CODE];
            $ids   = $this->checkExistedOrder($salesforceId);
        }
        if (!$ids && $order->getIncrementId()) {
            $this->addToCreateProductQueue($order);
        } else {
            $this->addToUpdateProductQueue($order, $ids['Id']);
        }
    }

    /**
     * @return array|mixed|string|null
     * @throws \Exception
     */
    public function syncQueue()
    {
        $response = $this->createOrders();
        $this->saveAttributes($this->createOrderIds, $response);
        $response += $this->createOrderItems();
        $response += $this->updateOrders();
        $this->unsetCreateProductQueue();
        $this->unsetUpdateOrderQueue();
        return $response;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    protected function addToCreateProductQueue($order)
    {
        $this->createOrderIds[] = [
            'mid'  => $order->getIncrementId(),
            'mObj' => $order
        ];
    }

    /**
     * @param $order
     * @param $sId
     */
    protected function addToUpdateProductQueue($order, $sId)
    {
        $this->updateOrderIds[] = [
            'mid'  => $order->getIncrementId(),
            'sid'  => $sId,
            'mObj' => $order
        ];
    }

    protected function unsetCreateProductQueue()
    {
        $this->createOrderIds = null;
    }

    protected function unsetUpdateOrderQueue()
    {
        $this->updateOrderIds = null;
    }

    /**
     * @return array|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createOrders()
    {
        if (is_null($this->createOrderIds)) {
            return [];
        }
        $params      = [];
        $pricebookId = $this->_getStandardPriceBookId();

        $accountIds = $this->getCustomerWithOrder();
        $orderIds   = $this->createOrderIds;
        foreach ($orderIds as $orderId) {
            $order = $orderId['mObj'];
            $date  = date('Y-m-d', strtotime($order->getCreatedAt()));
            $info  = $this->_data->getOrder($order, $this->_type);

            /*
                * Require Field:
                *
                * 1. AccountId
                * 2. EffectiveDate
                * 3. Status
                * 4. PriceBook2Id
             */
            $info += [
                'EffectiveDate' => $date,
                'Status'        => 'Draft',
                'Pricebook2Id'  => $pricebookId,
                'AccountId'     => isset($accountIds[$order->getId()]) ? $accountIds[$order->getId()] : ''
            ];

            /*
             * Number identifying the purchase order: PoNumber
             */
//            $info     += [
//                'PoNumber' => $order->getIncrementId(),
//            ];
            $params[] = $info;
        }
        $paramsUTF8 = $this->utf8ize($params);
        $response   = $this->_job->sendBatchRequest('insert', $this->_type, $this->_json->serialize($paramsUTF8));
        $this->saveReports('create', $this->_type, $response, $this->createOrderIds);
        return $response;
    }

    /**
     * @return array|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateOrders()
    {
        if (is_null($this->updateOrderIds)) {
            return [];
        }
        $params      = [];
        $orderIds    = $this->updateOrderIds;
        $pricebookId = $this->_getStandardPriceBookId();
        foreach ($orderIds as $orderId) {
            $order    = $orderId['mObj'];
            $date     = date('Y-m-d', strtotime($order->getCreatedAt()));
            $info     = $this->_data->getOrder($order, $this->_type);
            $info     += [
//                'PoNumber'      => $orderId['mid'],
'EffectiveDate' => $date,
'Status'        => 'Draft',
'Pricebook2Id'  => $pricebookId,
'Id'            => $orderId['sid']
            ];
            $params[] = $info;
        }
        $response = $this->_job->sendBatchRequest('update', $this->_type, $this->_json->serialize($params));
        $this->saveReports('update', $this->_type, $response, $this->updateOrderIds);
        return $response;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getCustomerWithOrder()
    {
        $accountIds               = [];
        $unsyncedOrderCustomerIds = [];
        $unsyncedEmails           = [];
        $unsyncedDataEmails       = [];
        $salesforceAccounts       = $this->_account->getAllSalesforceAccount();
        $orderIds                 = $this->createOrderIds;
        foreach ($orderIds as $orderId) {
            /** @var \Magento\Sales\Model\Order $order */
            $order      = $orderId['mObj'];
            $orderId    = $order->getId();
            $customerId = $order->getCustomerId();
            $email      = $order->getCustomerEmail();

            if ($customerId && isset($salesforceAccounts[$email])) {
                $accountIds[$orderId] = $salesforceAccounts[$email]['Id'];
            } elseif ($customerId && !isset($salesforceAccounts[$email])) {
                if (isset($unsyncedOrderCustomerIds[$customerId])) {
                    $unsyncedOrderCustomerIds[$customerId]['order_ids'][] = $orderId;
                } else {
                    $customer                                             = $this->_customerFactory->create()->load($customerId);
                    $unsyncedOrderCustomerIds[$customerId]                = [
                        'mid'  => $customerId,
                        'mObj' => $customer
                    ];
                    $unsyncedOrderCustomerIds[$customerId]['order_ids'][] = $orderId;
                }
            } else {
                if (isset($unsyncedEmails[$email])) {
                    $unsyncedEmails[$email]['order_ids'][] = $orderId;
                } else {
                    $unsyncedEmails[$email]                = ['email' => $email];
                    $unsyncedEmails[$email]['order_ids'][] = $orderId;
                }
                $address = $order->getBillingAddress();
                if (!$address) {
                    $address = $order->getShippingAddress();
                }
                $data                       = [
                    'Email'     => $email,
                    'FirstName' => $address->getFirstname(),
                    'LastName'  => $address->getLastname(),
                ];
                $unsyncedDataEmails[$email] = $data;
            }
        }
        if (!empty($unsyncedOrderCustomerIds)) {
            $unsyncedCustomerIds = array_values($unsyncedOrderCustomerIds);
            $response            = $this->_account->sendAccountsRequest($unsyncedCustomerIds, 'insert');
            $this->_contact->sendContactsRequest($unsyncedCustomerIds, 'insert');
            if (is_array($response)) {
                $total = count($response);
                for ($i = 0; $i < $total; $i++) {
                    if (isset($response[$i]['success']) && $response[$i]['success']) {
                        foreach ($unsyncedOrderCustomerIds[$unsyncedCustomerIds[$i]['mid']]['order_ids'] as $order_id) {
                            $accountIds[$order_id] = $response[$i]['id'];
                        }
                    }
                }
            }
        }
        if (!empty($unsyncedEmails)) {
            $response = $this->_account->syncByEmails($unsyncedEmails, 'insert');
            $this->_contact->syncByEmails($unsyncedDataEmails, 'insert');
            $accountIds += $response;
        }

        return $accountIds;
    }

    /**
     * @return array|mixed|string
     * @throws \Exception
     */
    protected function createOrderItems()
    {
        if (is_null($this->createOrderIds)) {
            return [];
        }
        $params   = [];
        $itemIds  = [];
        $orderIds = $this->createOrderIds;
        foreach ($orderIds as $orderId) {
            /** @var \Magento\Sales\Model\Order $order */
            $order   = $orderId['mObj'];
            $orderId = $order->getData(self::SALESFORCE_ORDER_ATTRIBUTE_CODE);
            /*
             * Sync OrderItem
             */
            foreach ($order->getAllVisibleItems() as $item) {
                $qty   = $item->getQtyOrdered();
                $price = $item->getPrice() - $item->getDiscountAmount() / $qty;

                if ($price > 0) {
                    // 5. Get productId
                    $productId = $item->getProduct()->getData(Product::SALESFORCE_PRODUCT_ATTRIBUTE_CODE);
                    if (!$productId) {
                        $productId = $this->_product->sync($item->getProductId());
                    }
                    if ($productId && $orderId) {
                        $pricebookEntryId = $this->searchRecords('PricebookEntry', 'Product2Id', $productId);
                        if (!$pricebookEntryId) {
                            $productId        = $this->_product->sync($item->getProductId());
                            $pricebookEntryId = $this->searchRecords('PricebookEntry', 'Product2Id', $productId);
                        }

                        $info      = [
                            'PricebookEntryId' => $pricebookEntryId,
                            'OrderId'          => $orderId,
                            'Quantity'         => $qty,
                            'UnitPrice'        => $price,
                        ];
                        $params[]  = $info;
                        $itemIds[] = ['mid' => $item->getProductId()];
                    }
                }
            }
            if ($taxInfo = $this->getTaxItemInfo($order, $orderId)) {
                $params[]  = $taxInfo;
                $itemIds[] = ['mid' => 'TAX'];
            }
            if ($shippingInfo = $this->getShippingItemInfo($order, $orderId)) {
                $params[]  = $shippingInfo;
                $itemIds[] = ['mid' => 'SHIPPING'];
            }
        }
        $response = $this->_job->sendBatchRequest('insert', 'OrderItem', $this->_json->serialize($params));
        $this->saveReports('create', 'OrderItem', $response, $itemIds);
        return $response;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string $orderId
     * @return array|null
     */
    public function getTaxItemInfo($order, $orderId)
    {
        $taxAmount = $order->getTaxAmount();
        if ($taxAmount > 0) {
            $info = [
                'PricebookEntryId' => $this->_scopeConfig->getValue(Product::XML_TAX_PRICEBOOKENTRY_ID_PATH),
                'OrderId'          => $orderId,
                'Quantity'         => 1,
                'UnitPrice'        => $taxAmount,
            ];
            return $info;
        }
        return null;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param string $orderId
     * @return array|null
     */
    public function getShippingItemInfo($order, $orderId)
    {
        $shippingAmount = $order->getShippingAmount();
        if ($shippingAmount > 0) {
            $info = [
                'PricebookEntryId' => $this->_scopeConfig->getValue(Product::XML_SHIPPING_PRICEBOOKENTRY_ID_PATH),
                'OrderId'          => $orderId,
                'Quantity'         => 1,
                'UnitPrice'        => $shippingAmount,
            ];
            return $info;
        }
        return null;
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
     * @param \Magento\Sales\Model\Order $order
     * @param $salesforceId
     * @throws \Exception
     */
    protected function saveAttribute($order, $salesforceId)
    {
        $resource = $order->getResource();
        $order->setData(self::SALESFORCE_ORDER_ATTRIBUTE_CODE, $salesforceId);
        $resource->saveAttribute($order, self::SALESFORCE_ORDER_ATTRIBUTE_CODE);
    }

    /**
     * Check existed order
     * @param $orderIncrementId
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function checkExistedOrder($orderIncrementId)
    {
        $existedOrders = $this->getAllSalesforceOrder();
        return isset($existedOrders[$orderIncrementId]) ? $existedOrders[$orderIncrementId] : false;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getAllSalesforceOrder()
    {
        if (!is_null($this->existedOrders)) {
            return $this->existedOrders;
        }
        $allOrders     = [];
        $existedOrders = $this->dataGetter->getAllSalesforceOrders();
        foreach ($existedOrders as $key => $value) {
            $allOrders[$value['Id']] = $value;
        }
        $this->existedOrders = $allOrders;
        return $this->existedOrders;
    }

    /**
     * @return bool|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function _getStandardPriceBookId()
    {
        if (!$this->_standardPriceBookId) {
            $this->_standardPriceBookId = $this->searchRecords('Pricebook2', 'Name', 'Standard Price Book');
        }

        return $this->_standardPriceBookId;
    }
}
