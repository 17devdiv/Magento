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

namespace Magenest\Salesforce\Model\ResourceModel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Field
 *
 * @package Magenest\Salesforce\Model\ResourceModel
 */
class Field extends AbstractDb
{
    /**
     * @var AdapterInterface
     */
    protected $_connection;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_salesforce_field', 'id');
        $this->_connection = $this->getConnection();
    }

    /**
     * @param $type
     * @return array
     */
    public function getEavIdByType($type)
    {
        $select = $this->_connection->select()->from($this->getTable('eav_entity_type'))
            ->where('entity_type_code = ?', $type);
        $result = $this->_connection->fetchRow($select);
        return $result;
    }

    /**
     * @param $type
     * @return array
     */
    public function getFieldsByEntity($type)
    {
        $select = $this->_connection->select()->from($this->getTable('eav_attribute'))
            ->where('entity_type_id = ?', $type);
        $result = $this->_connection->fetchAll($select);
        return $result;
    }
}
