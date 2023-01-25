<?php

namespace Magenest\Salesforce\Observer;

use Magenest\Salesforce\Helper\Data;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\State;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ValidateSalesForceConnected
 * @package Magenest\Salesforce\Observer
 */
class ValidateSalesForceConnected implements ObserverInterface
{
    /**
     * @var array
     */
    protected $need_validate_path
        = [
            'salesforce_map_newmapping',
            'salesforce_report_index',
            'salesforce_request_index',
            'salesforce_queue_index'
        ];
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var State
     */
    protected $_appState;

    /**
     * ValidateSalesForceConnected constructor.
     * @param Data $helper
     * @param UrlInterface $url
     * @param State $state
     */
    public function __construct(
        Data $helper,
        UrlInterface $url,
        State $state
    )
    {
        $this->_appState   = $state;
        $this->_urlBuilder = $url;
        $this->helper      = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $fullActionName = $observer->getEvent()->getRequest()->getFullActionName();
        if ($this->_appState->getAreaCode() === 'adminhtml' && in_array($fullActionName, $this->need_validate_path) && !$this->helper->isSalesForceCRMConnected()) {
            $controllerAction = $observer->getEvent()->getControllerAction();
            $controllerAction->getResponse()->setRedirect($this->_urlBuilder->getUrl(Data::CONFIGURATION_SALESFORCE_SECTION_PATH));
        }
    }
}