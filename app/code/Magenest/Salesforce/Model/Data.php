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

namespace Magenest\Salesforce\Model;

use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Directory\Model\Country;
use Magento\Sales\Model\Order;
use Magento\Tax\Model\ClassModel;

/**
 * Data Model
 *
 * @author Thao Pham <thaophamit@gmail.com>
 */
class Data
{
    protected $_salesforceFields = null;

    /** @var FieldFactory  */
    protected $_fieldFactory;

    /**
     * @var \Magenest\Salesforce\Model\MapFactory
     */
    protected $_mapFactory;

    /** @var ResourceModel\Map\CollectionFactory  */
    protected $_mapCollection;

    /**
     * @var \Magenest\Salesforce\Model\Field
     */
    protected $_field;

    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $_country;

    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $_tax;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $_stockItemRepository;

	/**
	 * @var \Magento\Customer\Model\GroupFactory
	 */
    protected $_customerGroup;

    /** @var \Magento\Framework\Serialize\Serializer\Serialize  */
    protected $_serializer;

    /** @var \Magento\Framework\Serialize\Serializer\Json  */
    protected $_json;

    /** @var \Magenest\Salesforce\Logger\Logger  */
    protected $_logger;

    /**
     * Data constructor.
     *
     * @param FieldFactory $fieldFactory
     * @param MapFactory $map
     * @param ResourceModel\Map\CollectionFactory $collectionFactory
     * @param Field $field
     * @param Country $country
     * @param ClassModel $tax
     * @param StockItemRepository $stockItemRepository
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magenest\Salesforce\Logger\Logger $logger
     */
	public function __construct(
	    \Magenest\Salesforce\Model\FieldFactory $fieldFactory,
		MapFactory $map,
		\Magenest\Salesforce\Model\ResourceModel\Map\CollectionFactory $collectionFactory,
		Field $field,
		Country $country,
		ClassModel $tax,
		StockItemRepository $stockItemRepository,
		\Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magenest\Salesforce\Logger\Logger $logger
	) {
	    $this->_fieldFactory = $fieldFactory;
		$this->_customerGroup = $groupFactory;
		$this->_mapFactory = $map;
		$this->_mapCollection = $collectionFactory;
		$this->_field = $field;
		$this->_country = $country;
		$this->_tax = $tax;
		$this->_stockItemRepository = $stockItemRepository;
		$this->_serializer = $serialize;
		$this->_json = $json;
		$this->_logger = $logger;
	}

    /**
     * @param $data
     * @param $_type
     *
     * @return array
     * @throws \Exception
     */
    public function getMapping($data, $_type)
    {
        $collection = $this->_mapCollection->create()
            ->addFieldToFilter('type', $_type)
            ->addFieldToFilter('status', 1);
        $map        = [];
        $result     = [];

        /** @var Map $value */
        foreach ($collection as $key => $value) {
            $salesforce       = $value->getSalesforce();
            $magento          = $value->getMagento();
            $map[$salesforce] = $magento;
        }
        try{
            $salesforceFieldType = $this->getSalesforceFieldType($_type);
            /** @var string $value */
            foreach ($map as $key => $value) {
                if (isset($data[$value]) && $data[$value]) {
                    $result[$key] = $this->convertData($salesforceFieldType[$key],$data[$value]);
                }
            }
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
        }
        return $result;
    }

    /**
     * @param $type
     *
     * @return array|bool|float|int|mixed|string|null
     * @throws \Exception
     */
    protected function getSalesforceFieldType($type)
    {
        if($this->_salesforceFields == null){
            $fieldModel        = $this->_fieldFactory->create();
            $fieldModel->loadByTable($type, true);
            $salesFields = $fieldModel->getSalesforce();
            $salesFieldsArr = $this->_json->unserialize($salesFields);
            if(is_array($salesFieldsArr) && count($salesFieldsArr) == 2 && isset($salesFieldsArr['types'])){
                $this->_salesforceFields = $this->_serializer->unserialize($salesFieldsArr['types']);
            }else{
                $this->_salesforceFields = [];
            }
        }
        return $this->_salesforceFields;
    }

    /**
     * @param $type
     * @param $rawData
     *
     * @return bool|false|float|int|string
     */
    protected function convertData($type, $rawData)
    {
        try{
            switch ($type){
                case 'boolean':
                    $data = $rawData ? true : false;
                    break;
                case 'currency':
                case 'percent':
                case 'double':
                    $data = doubleval($rawData);
                    break;
                case 'int':
                    $data = intval($rawData);
                    break;
                case 'date':
                    $data = date('Y-m-d', strtotime($rawData));
                    break;
                case 'datetime':
                    $data = date('Y-m-d H:i:s', strtotime($rawData));
                    break;
                default:
                    $data = (string)$rawData;
                    break;
            }
        }catch (\Exception $exception){
            $data = $rawData;
            $this->_logger->critical($exception->getMessage());
        }
        return $data;
    }

    /**
     * Get Country Name
     *
     * @param  string $id
     * @return string
     */
    public function getCountryName($id)
    {
        $model = $this->_country->loadByCode($id);

        return $model->getName();
    }

    /**
     * Get all data of Customer
     * @param $model
     * @param $_type
     *
     * @return array
     * @throws \Exception
     */
    public function getCustomer($model, $_type)
    {
        $this->_field->setType($_type);
        $magento_fields = $this->_field->getMagentoFields();
        $data           = [];
        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'bill_' && $model->getDefaultBillingAddress()) {
                $value   = substr($key, 5);
                $billing = $model->getDefaultBillingAddress();
                if ($key == "bill_vat_id") {
                    $data[$key] = $billing->getData('vat_id');
                } else {
                    $data[$key] = $billing->getData($value);
                }
            } elseif ($sub == 'ship_' && $model->getDefaultShippingAddress()) {
                $value    = substr($key, 5);
                $shipping = $model->getDefaultShippingAddress();
                if ($key == "ship_vat_id") {
                    $data[$key] = $shipping->getData('vat_id');
                } else {
                    $data[$key] = $shipping->getData($value);
                }
            } elseif ($key == 'group_id') {
            	$groupId = $model->getGroupId();
            	$groupName = $this->_customerGroup->create()->load($groupId)->getCode();
            	$data[$key] = $groupName;
            } else {
                $data[$key] = $model->getData($key);
            }
        }
        $this->fixIsActiveField($data);

        if (!empty($data['bill_country_id'])) {
            $country_id              = $data['bill_country_id'];
            $data['bill_country_id'] = $this->getCountryName($country_id);
        }

        if (!empty($data['ship_country_id'])) {
            $country_id              = $data['ship_country_id'];
            $data['ship_country_id'] = $this->getCountryName($country_id);
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    /**
     * Pass data of CatalogRule to array and return after mapping
     * @param $model
     * @param $_type
     *
     * @return array
     * @throws \Exception
     */
    public function getCampaign($model, $_type)
    {
        $this->_field->setType($_type);
        $magento_fields = $this->_field->getMagentoFields();
        $data           = [];

        // Pass data of catalog rule price to array
        foreach ($magento_fields as $key => $item) {
            $data[$key] = $model->getData($key);
        }

        $this->fixIsActiveField($data);

        $action = [
            'by_percent' => 'By Percentage of the Original Price',
            'by_fixed'   => 'By Fixed Amount',
            'to_percent' => 'To Percentage of the Original Price',
            'to_fixed'   => 'To Fixed Amount',
        ];
        if (!empty($data['simple_action'])) {
            foreach ($action as $key => $value) {
                if ($data['simple_action'] == $key) {
                    $data['simple_action'] = $value;
                }
            }
        }

        if (isset($data['sub_is_enable']) && $data['sub_is_enable'] == 1) {
            $data['sub_is_enable'] = 'Yes';
            foreach ($action as $key => $value) {
                if ($data['simple_action'] == $key) {
                    $data['simple_action'] = $value;
                }
            }
        } else {
            $data['sub_is_enable'] = 'No';
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    /**
     * Pass data of Order to array and return mapping
     * @param $model
     * @param $_type
     *
     * @return array
     * @throws \Exception
     */
    public function getOrder($model, $_type)
    {
        $this->_field->setType($_type);
        $magento_fields = $this->_field->getMagentoFields();
        $data           = [];

        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'bill_') {
                $billing    = $model->getBillingAddress();
                $data[$key] = $billing ? $billing->getData(substr($key, 5)) : null;
            } elseif ($sub == 'ship_') {
                $shipping   = $model->getShippingAddress();
                $data[$key] = $shipping ? $shipping->getData(substr($key, 5)) : null;
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        if (!empty($data['bill_country_id'])) {
            $country_id              = $data['bill_country_id'];
            $data['bill_country_id'] = $this->getCountryName($country_id);;
        }

        if (!empty($data['ship_country_id'])) {
            $country_id              = $data['ship_country_id'];
            $data['ship_country_id'] = $this->getCountryName($country_id);;
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }


    /**
     * Pass data of Product to array and return after mapping
     * @param $model
     * @param $_type
     *
     * @return array
     * @throws \Exception
     */
    public function getProduct($model, $_type)
    {
        $this->_field->setType($_type);
        $magento_fields = $this->_field->getMagentoFields();
        $data           = [];

        // ..........Pass data of Product to array..........
        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'stock') {
                $stockItem  = $model->getExtensionAttributes()->getStockItem();
                $data[$key] = $stockItem->getData(substr($key, 6));
            } elseif ($key == 'quantity_and_stock_status') {
                $qtyAndStockStatus = $model->getData($key);
                $data[$key]        = isset($qtyAndStockStatus['qty']) ? $qtyAndStockStatus['qty'] : 0;
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        $this->fixIsActiveField($data, 'status');

        if (!empty($data['country_of_manufacture'])) {
            $country_id                     = $data['country_of_manufacture'];
            $data['country_of_manufacture'] = $this->getCountryName($country_id);
        }

        if (!empty($data['tax_class_id'])) {
            $tax_id = $data['tax_class_id'];
            if ($tax_id == 0) {
                $data['tax_class_id'] = "None";
            } else {
                $data['tax_class_id'] = $this->_tax->load($tax_id)->getClassName();
            }
        }

        // .............End pass data...............
        // 4. Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    /**
     * Pass data of Invoice to array and return after mapping
     * @param $model
     * @param $_type
     *
     * @return array
     * @throws \Exception
     */
    public function getInvoice($model, $_type)
    {
        $this->_field->setType($_type);
        $magento_fields = $this->_field->getMagentoFields();
        $data           = [];

        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'bill_') {
                $billing = $model->getBillingAddress();
                if ($billing) {
                    $data[$key] = $billing->getData(substr($key, 5));
                }
            } elseif ($sub == 'ship_') {
                $shipping = $model->getShippingAddress();
                if ($shipping) {
                    $data[$key] = $shipping->getData(substr($key, 5));
                }
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        $data['order_increment_id'] = $model->getOrderIncrementId();
        if (!empty($data['bill_country_id'])) {
            $country_id              = $data['bill_country_id'];
            $data['bill_country_id'] = $this->getCountryName($country_id);;
        }

        if (!empty($data['ship_country_id'])) {
            $country_id              = $data['ship_country_id'];
            $data['ship_country_id'] = $this->getCountryName($country_id);
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    /**
     * @param $model
     * @param $_type
     *
     * @return array
     * @throws \Exception
     */
    public function getOpportunity($model, $_type)
    {
        $this->_field->setType($_type);
        $magento_fields = $this->_field->getMagentoFields();
        $data           = [];

        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'bill_') {
                $billing = $model->getBillingAddress();
                if ($billing) {
                    $data[$key] = $billing->getData(substr($key, 5));
                }
            } elseif ($sub == 'ship_') {
                $shipping = $model->getShippingAddress();
                if ($shipping) {
                    $data[$key] = $shipping->getData(substr($key, 5));
                }
            } elseif ($key == 'payment_method') {
                $data[$key] = $model->getPayment()->getMethod();
            } elseif ($key == 'cart_all') {
                $items = [];
                foreach ($model->getItems() as $orderItem) {
                    $items[] = [
                        'product_id' => $orderItem->getProductId(),
                        'name'       => $orderItem->getName(),
                        'sku'        => $orderItem->getSku(),
                        'qty'        => $orderItem->getQtyOrdered(),
                        'unit_price' => $orderItem->getPrice()
                    ];
                }
                $data[$key] = json_encode($items);
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        $data['order_increment_id'] = $model->getIncrementId();
        if (!empty($data['bill_country_id'])) {
            $country_id              = $data['bill_country_id'];
            $data['bill_country_id'] = $this->getCountryName($country_id);
        }

        if (!empty($data['ship_country_id'])) {
            $country_id              = $data['ship_country_id'];
            $data['ship_country_id'] = $this->getCountryName($country_id);
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    /**
     * Fix value for is_active field in data
     *
     * @param array $data
     * @param string $fieldName
     */
    private function fixIsActiveField(&$data, $fieldName = 'is_active')
    {
        if (isset($data[$fieldName])) {
            $data[$fieldName] = $data[$fieldName] == 1 ? true : false;
        }
    }
}
