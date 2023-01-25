<?php
namespace Magenest\Salesforce\Model\Config\Source;

/**
 * Class ConnectEnvironment
 * @package Magenest\Salesforce\Model\Config\Source
 */
class ConnectEnvironment implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Options array
     *
     * @var array
     */
    protected $_options = [ 1 => 'Production Environment', 2 => 'Sandbox Environment'];

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
