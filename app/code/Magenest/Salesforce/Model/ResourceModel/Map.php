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
 * Class Map
 *
 * @package Magenest\Salesforce\Model\ResourceModel
 */
class Map extends AbstractDb
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
        $this->_init('magenest_salesforce_map', 'id');
        $this->_connection = $this->getConnection();
    }

    /**
     * @param \Magenest\Salesforce\Model\Map $map
     * @param $type
     * @param $field
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadMagentoFieldByType(\Magenest\Salesforce\Model\Map $map, $type, $field)
    {
        $bind   = ['type' => $type, 'magento' => $field];
        $select = $this->_connection->select()->from($this->getMainTable())->where('type = :type')->where('magento = :magento');
        $id     = $this->_connection->fetchOne($select, $bind);
        if ($id) {
            $this->load($map, $id);
        } else {
            $map->setData([]);
        }
        return $this;
    }
}
