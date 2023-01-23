<?php
/**
 * Dotsquares
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @author Dotsquares
 * @package Dotsquares_GDPR
 * @copyright Copyright (c) Dotsquares (https://www.dotsquares.com/)
 */
 
namespace Dotsquares\Gdpr\Block\System\Config;

class Checkbox extends \Magento\Config\Block\System\Config\Form\Field
{
    const CONFIG_PATH = 'cusotmerdelete/general/willcustomerdata';

    protected $_template = 'Dotsquares_Gdpr::system/config/checkbox.phtml';

    protected $_values = null;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())->setHtmlId($element->getHtmlId());
        return $this->_toHtml();
    }
     
    public function getValues()
    {
        $values = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($objectManager->create('Dotsquares\Gdpr\Model\Config\Source\Checkbox')->toOptionArray() as $value) {
            $values[$value['value']] = $value['label'];
        }
        return $values;
    }
    
    public function getIsChecked($name)
    {
        return in_array($name, $this->getCheckedValues());
    }
    
    public function getCheckedValues()
    {
        if ($this->_values == '') {
            $data = $this->getConfigData();
            if (isset($data[self::CONFIG_PATH])) {
                $data = $data[self::CONFIG_PATH];
            } else {
                $data = '';
            }
            $this->_values = explode(',', $data);
        }
        return $this->_values;
    }
}