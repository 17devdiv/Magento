<?php

namespace Magenest\Salesforce\Model;

use Magenest\Salesforce\Model\ResourceModel\Field as ResourceField;
use Magenest\Salesforce\Model\ResourceModel\Field\Collection;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Serialize;

/**
 * Class Field
 * @package Magenest\Salesforce\Model
 */
class Field extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'map';

    /**
     * @var \Magenest\Salesforce\Model\Connector
     */
    protected $_connector;

    /**
     * @var array
     */
    protected $mage_field;

    /**
     * @var string
     */
    protected $mage_type;

    /**
     * @var string
     */
    protected $sales_type;

    /**
     * @var string
     */
    protected $sales_field;

    /**
     * @var array
     */
    protected $_eavEntities;

    /**
     * @var Serialize
     */
    protected $_serializer;

    /** @var \Magento\Framework\Serialize\Serializer\Json  */
    protected $_json;

    /**
     * Field constructor.
     *
     * @param Serialize $serialize
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param Context $context
     * @param Registry $registry
     * @param ResourceField $resource
     * @param Collection $resourceCollection
     * @param Connector $connector
     * @param array $data
     */
    public function __construct(
        Serialize $serialize,
        \Magento\Framework\Serialize\Serializer\Json $json,
        Context $context,
        Registry $registry,
        ResourceField $resource,
        Collection $resourceCollection,
        Connector $connector,
        array $data = []
    ) {

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_connector  = $connector;
        $this->_serializer = $serialize;
        $this->_json = $json;
    }

    /**
     * Initialize resources
     * @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\Salesforce\Model\ResourceModel\Field');
        $this->_prepareData();
    }

    private function _prepareData()
    {
        $entitiesType = ['customer', 'customer_address', 'catalog_product'];
        foreach ($entitiesType as $type) {
            $this->_eavEntities[$type] = new DataObject($this->getResource()->getEavIdByType($type));
        }
    }

    /**
     * @param bool $update
     *
     * @return mixed
     * @throws \Exception
     */
    public function getSalesforceFields($update = false)
    {
        $salesFields = $this->getSalesforce(); // method getData('salesforce);
        $salesFieldsArr = $this->_json->unserialize($salesFields);
        if(is_array($salesFieldsArr) && count($salesFieldsArr) == 2 && isset($salesFieldsArr['fields'])){
            $results = $this->decodeSalesforceFields($salesFieldsArr['fields']);
        }else{
            $results = $this->decodeSalesforceFields($salesFields);
        }
        return $results;
    }

    /**
     * @param $salesFields
     * @param bool $update
     *
     * @return array|bool|float|int|mixed|string|null
     * @throws \Exception
     */
    public function decodeSalesforceFields($salesFields, $update = false)
    {
        if (!empty($salesFields) && !$update) {
            return $this->_serializer->unserialize($salesFields);
        } else {
            $this->setSalesforceFields($this->sales_type);
            return $this->_serializer->unserialize($this->sales_field);
        }
    }

    /**
     * @param $table
     * @param bool|false $update
     *
     * @return $this
     * @throws \Exception
     */
    public function loadByTable($table, $update = false)
    {
        $this->load($table, 'type');
        if (!$this->getId() || $update) {
            $this->setType($table);
            $this->saveFields($update);
        } else {
            $this->sales_type = $table;
            $this->mage_type  = $this->getData('magento');
        }

        return $this;
    }

    /**
     * @param $sales_type
     *
     * @return $this
     * @throws \Exception
     */
    public function setSalesforceFields($sales_type)
    {
        $this->sales_field = $this->_connector->getFields($sales_type);

        return $this;
    }

    /**
     * Set Type of field
     *
     * @param $type
     *
     * @return Field
     */
    public function setType($type)
    {
        $this->sales_type = $type;
        $table            = $this->getAllTable();
        if (!empty($table[$type])) {
            $this->mage_type = $table[$type];
        }

        return $this;
    }

    /**
     * Map two table of Magento and Salesforce
     * @return array
     */
    public function getAllTable()
    {
        $table = [
            'Account'     => 'customer',
            'Contact'     => 'customer',
            'Campaign'    => 'catalogrule',
            'Lead'        => 'customer',
            'Product2'    => 'product',
            'Order'       => 'order',
            'Opportunity' => 'opportunity'
        ];

        return $table;
    }

    /**
     * Return option table to select in Admin
     * @return array
     */
    public function changeFields()
    {
        $table = $this->getAllTable();
        $data  = ['' => '--- Select Option ---'];
        foreach ($table as $key => $value) {
            $length = strlen($key);
            $subkey = substr($key, ($length - 3), $length);
            if ($subkey == '__c') {
                $data[$key] = substr($key, 0, ($length - 3));
            } elseif ($key == 'Product2') {
                $data[$key] = 'Product';
            } else {
                $data[$key] = $key;
            }
        }

        return $data;
    }

    /**
     * @param bool|false $update
     *
     * @return $this
     * @throws \Exception
     */
    public function saveFields($update = false)
    {
        $this->setSalesforceFields($this->sales_type);
        $data = [
            'type'       => $this->sales_type,
            'salesforce' => $this->sales_field,
            'magento'    => $this->mage_type,
            'status'     => 1,
        ];

        if ($this->getId() && $update) {
            $this->addData($data);
        } else {
            $this->setData($data);
        }

        $this->save();

        return $this;
    }

    /**
     * Get Magento Field
     * @return array
     */
    public function getMagentoFields()
    {
        if (is_null($this->mage_field)) {
            $this->setMagentoFields($this->mage_type);
        }

        return $this->mage_field;
    }

    /**
     * Set field magento to map
     *
     * @param  $table
     *
     * @return Field
     */
    public function setMagentoFields($table)
    {
        $magentoFields = [];
        switch ($table) {
            case 'customer':
                $customerFields                = $this->getEavEntityFields('customer');
                $customerAddressFields         = $this->getEavEntityFields('customer_address');
                $customerShippingAddressFields = $this->getCustomerAddressFields($customerAddressFields, 'ship_', 'Shipping ');
                if(isset($customerShippingAddressFields['ship_region_id'])){
                    unset($customerShippingAddressFields['ship_region_id']);
                }
                $customerBillingAddressFields  = $this->getCustomerAddressFields($customerAddressFields, 'bill_', 'Billing ');
                $magentoFields                 = array_merge($customerFields, $customerShippingAddressFields, $customerBillingAddressFields);
                break;

            case 'catalogrule':
                $magentoFields = [
                    'rule_id'             => 'Rule Id',
                    'description'         => 'Description',
                    'from_date'           => 'From Date',
                    'to_date'             => 'To Date',
                    'is_active'           => 'Active',
                    'simple_action'       => 'Simple Action(Apply)',
                    'discount_amount'     => 'Discount Amount',
                    'sub_is_enable'       => 'Enable Discount to Subproducts',
                    'sub_simple_action'   => 'Subproducts Simple Action(Apply)',
                    'sub_discount_amount' => 'Subproducts Discount Amount',
                ];
                break;

            case 'product':
                $magentoFields = $this->getEavEntityFields('catalog_product');
                break;

            case 'order':
                $magentoFields = [
                    'entity_id' => 'ID',
                    'state' => 'State',
                    'status' => 'Status',
                    'coupon_code' => 'Coupon Code',
                    'coupon_rule_name' => 'Coupon Rule Name',
                    'increment_id' => 'Increment ID',
                    'created_at' => 'Created At',
                    'customer_id' => 'Customer ID',
                    'customer_firstname' => 'Customer First Name',
                    'customer_middlename' => 'Customer Middle Name',
                    'customer_lastname' => 'Customer Last Name',
                    'customer_email' => 'Customer Email',
                    'bill_firstname' => 'Billing First Name',
                    'bill_middlename' => 'Billing Middle Name',
                    'bill_lastname' => 'Billing Last Name',
                    'bill_company' => 'Billing Company',
                    'bill_street' => 'Billing Street',
                    'bill_city' => 'Billing City',
                    'bill_region' => 'Billing State/Province',
                    'bill_postcode' => 'Billing Zip/Postal Code',
                    'bill_telephone' => 'Billing Telephone',
                    'bill_country_id' => 'Billing Country',
                    'ship_firstname' => 'Shipping First Name',
                    'ship_middlename' => 'Shipping Middle Name',
                    'ship_lastname' => 'Shipping Last Name',
                    'ship_company' => 'Shipping Company',
                    'ship_street' => 'Shipping Street',
                    'ship_city' => 'Shipping City',
                    'ship_region' => 'Shipping State/Province',
                    'ship_postcode' => 'Shipping Zip/Postal Code',
                    'ship_country_id' => 'Shipping Country',
                    'shipping_amount' => 'Shipping Amount',
                    'shipping_description' => 'Shipping Description',
                    'order_currency_code' => 'Currency Code',
                    'total_item_count' => 'Total Item Count',
                    'store_currency_code' => 'Store Currency Code',
                    'shipping_discount_amount' => 'Shipping Discount Amount',
                    'discount_description' => 'Discount Description',
                    'shipping_method' => 'Shipping Method',
                    'store_name' => 'Store Name',
                    'discount_amount' => 'Discount Amount',
                    'tax_amount' => 'Tax Amount',
                    'subtotal' => 'Sub Total',
                    'grand_total' => 'Grand Total',
                    'remote_ip' => 'Remote IP',
                ];
                break;

            case 'invoice':
                $magentoFields = [
                    'entity_id' => 'ID',
                    'state' => 'State',
                    'status' => 'Status',
                    'coupon_code' => 'Coupon Code',
                    'coupon_rule_name' => 'Coupon Rule Name',
                    'increment_id' => 'Increment ID',
                    'created_at' => 'Created At',
                    'customer_id' => 'Customer ID',
                    'customer_firstname' => 'Customer First Name',
                    'customer_middlename' => 'Customer Middle Name',
                    'customer_lastname' => 'Customer Last Name',
                    'customer_email' => 'Customer Email',
                    'bill_firstname' => 'Billing First Name',
                    'bill_middlename' => 'Billing Middle Name',
                    'bill_lastname' => 'Billing Last Name',
                    'bill_company' => 'Billing Company',
                    'bill_street' => 'Billing Street',
                    'bill_city' => 'Billing City',
                    'bill_region' => 'Billing State/Province',
                    'bill_postcode' => 'Billing Zip/Postal Code',
                    'bill_telephone' => 'Billing Telephone',
                    'bill_country_id' => 'Billing Country',
                    'ship_firstname' => 'Shipping First Name',
                    'ship_middlename' => 'Shipping Middle Name',
                    'ship_lastname' => 'Shipping Last Name',
                    'ship_company' => 'Shipping Company',
                    'ship_street' => 'Shipping Street',
                    'ship_city' => 'Shipping City',
                    'ship_region' => 'Shipping State/Province',
                    'ship_postcode' => 'Shipping Zip/Postal Code',
                    'ship_country_id' => 'Shipping Country',
                    'shipping_amount' => 'Shipping Amount',
                    'shipping_description' => 'Shipping Description',
                    'order_currency_code' => 'Currency Code',
                    'total_item_count' => 'Total Item Count',
                    'store_currency_code' => 'Store Currency Code',
                    'shipping_discount_amount' => 'Shipping Discount Amount',
                    'discount_description' => 'Discount Description',
                    'shipping_method' => 'Shipping Method',
                    'store_name' => 'Store Name',
                    'discount_amount' => 'Discount Amount',
                    'tax_amount' => 'Tax Amount',
                    'subtotal' => 'Sub Total',
                    'grand_total' => 'Grand Total',
                    'remote_ip' => 'Remote IP',
                ];
                break;
            case 'opportunity':
                $magentoFields = [
                    'entity_id' => 'ID',
                    'state' => 'State',
                    'status' => 'Status',
                    'coupon_code' => 'Coupon Code',
                    'coupon_rule_name' => 'Coupon Rule Name',
                    'increment_id' => 'Increment ID',
                    'created_at' => 'Created At',
                    'customer_id' => 'Customer ID',
                    'customer_firstname' => 'Customer First Name',
                    'customer_middlename' => 'Customer Middle Name',
                    'customer_lastname' => 'Customer Last Name',
                    'customer_email' => 'Customer Email',
                    'bill_firstname' => 'Billing First Name',
                    'bill_middlename' => 'Billing Middle Name',
                    'bill_lastname' => 'Billing Last Name',
                    'bill_company' => 'Billing Company',
                    'bill_street' => 'Billing Street',
                    'bill_city' => 'Billing City',
                    'bill_region' => 'Billing State/Province',
                    'bill_postcode' => 'Billing Zip/Postal Code',
                    'bill_telephone' => 'Billing Telephone',
                    'bill_country_id' => 'Billing Country',
                    'ship_firstname' => 'Shipping First Name',
                    'ship_middlename' => 'Shipping Middle Name',
                    'ship_lastname' => 'Shipping Last Name',
                    'ship_company' => 'Shipping Company',
                    'ship_street' => 'Shipping Street',
                    'ship_city' => 'Shipping City',
                    'ship_region' => 'Shipping State/Province',
                    'ship_postcode' => 'Shipping Zip/Postal Code',
                    'ship_country_id' => 'Shipping Country',
                    'shipping_amount' => 'Shipping Amount',
                    'shipping_description' => 'Shipping Description',
                    'order_currency_code' => 'Currency Code',
                    'total_item_count' => 'Total Item Count',
                    'store_currency_code' => 'Store Currency Code',
                    'shipping_discount_amount' => 'Shipping Discount Amount',
                    'discount_description' => 'Discount Description',
                    'shipping_method' => 'Shipping Method',
                    'store_name' => 'Store Name',
                    'discount_amount' => 'Discount Amount',
                    'tax_amount' => 'Tax Amount',
                    'subtotal' => 'Sub Total',
                    'grand_total' => 'Grand Total',
                    'remote_ip' => 'Remote IP',
                ];
                break;
            default:
                break;
        }

        $this->mage_field = $magentoFields;

        return $this;
    }

    /**
     * @param string $entityType
     *
     * @return array
     */
    private function getEavEntityFields($entityType)
    {
        $fields          = $this->getResource()->getFieldsByEntity($this->_eavEntities[$entityType]->getEntityTypeId());
        $attributesCode  = array_column($fields, 'attribute_code');
        $attributesLabel = array_column($fields, 'frontend_label');

        return $this->fixFields(array_combine($attributesCode, $attributesLabel));
    }

    /**
     * @param array $fields
     * @param string $keyType
     * @param string $valueType
     *
     * @return array
     */
    private function getCustomerAddressFields($fields, $keyType = '', $valueType = '')
    {
        return array_combine(
            array_map(function ($key) use ($keyType) {
                return $keyType . $key;
            }, array_keys($fields)),
            array_map(function ($key) use ($valueType) {
                return $valueType . $key;
            }, array_values($fields))
        );
    }

    /**
     * @param array $fields
     *
     * @return array fixed fields
     */
    private function fixFields($fields)
    {
        $fixedFields = $fields;
        foreach ($fields as $attributeCode => $attributeLabel) {
            if (!$attributeCode || !$attributeLabel) {
                unset($fixedFields[$attributeCode]);
            }
        }

        return $fixedFields;
    }
}
