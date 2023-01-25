<?php

namespace Magenest\Salesforce\Block\System\Config\Version;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Info extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;

    /** @var \Magenest\Salesforce\Helper\Data  */
    protected $helper;

    /**
     * Info constructor.
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\View\Helper\Js $jsHelper
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magenest\Salesforce\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magenest\Salesforce\Helper\Data $helper,
        array $data = []
    )
    {

        parent::__construct($context, $authSession, $jsHelper, $data);

        $this->_layoutFactory = $layoutFactory;
        $this->helper         = $helper;
        $this->_scopeConfig   = $context->getScopeConfig();
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);

        $html .= $this->_getVersion();
        $html .= $this->_getSupportLinks();

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $layout = $this->_layoutFactory->create();

            $this->_fieldRenderer = $layout->createBlock(
                'Magento\Config\Block\System\Config\Form\Field'
            );
        }

        return $this->_fieldRenderer;
    }

    /**
     * @return string
     */
    protected function _getVersion()
    {
        $value = '<table style="margin-left: 200px;">';
        $value .= '<tr>';
        $value .= '<td style="width: 200px; font-weight: bold">Version</td>';
        $value .= '<td>' . $this->helper->getModuleVersion() . '</td>';
        $value .= '</tr>';
        $value .= '</table>';

        return $value;
    }

    /**
     * @return string
     */
    protected function _getSupportLinks()
    {
        $supportPortal = [
            'Installation Guide' => 'http://www.confluence.izysync.com/display/DOC/1.+Salesforce+CRM+Integration+Installation+Guides',
            'User Guide' => 'http://www.confluence.izysync.com/display/DOC/2.+Salesforce+CRM+Integration+User+Guides',
            'Support Portal' => 'http://servicedesk.izysync.com/servicedesk/customer/portal/20'
        ];
        $value         = '<table style="margin-left: 200px;">';
        $value         .= '<tr>';
        $value         .= '<td style="width: 200px; font-weight: bold">Support Links</td>';

        $value .= '<td><table>';
        foreach ($supportPortal as $k => $v) {
            $value
                .= '<tr>' .
                '<td style="width: 100px;padding: 0">' . $k . '</td>' .
                '<td style="width: 400px; padding: 0"><a target="_blank" href="' . $v . '">Go to ' . $k . '</a></td>' .
                '</tr>';
        }
        $value .= '</table></td>';
        $value .= '</tr>';
        $value .= '</table>';

        return $value;
    }
}
