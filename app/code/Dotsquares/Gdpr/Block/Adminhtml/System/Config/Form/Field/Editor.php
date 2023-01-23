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
 
namespace Dotsquares\Gdpr\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;
  
class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_coreRegistry;

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
        ) {
     $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context);
    }
    /* public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    } */
	
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $config = $this->_wysiwygConfig->getConfig();
        $element->setWysiwyg(true);
        $element->setConfig($config);
        return parent::_getElementHtml($element);
    }

    /* protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setWysiwyg(true);
        $element->setConfig($this->_wysiwygConfig->getConfig($element));
        return parent::_getElementHtml($element);
    } */
}