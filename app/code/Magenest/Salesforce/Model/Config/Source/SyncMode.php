<?php

namespace Magenest\Salesforce\Model\Config\Source;
/**
 * Class SyncMode
 * @package Magenest\Salesforce\Model\Config\Source
 */
class SyncMode implements \Magento\Framework\Option\ArrayInterface
{

    const ADD_TO_QUEUE = 1;
    const AUTO_SYNC    = 2;
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = [self::ADD_TO_QUEUE => 'Add to Queue', self::AUTO_SYNC => 'Auto Sync'];

    /**
     * Return options array
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_options;
        return $options;
    }
}
