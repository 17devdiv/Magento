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
 
namespace Dotsquares\Gdpr\Block;

class Deletecustomer extends \Magento\Framework\View\Element\Template
{
    protected $templateProcessor;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Sales\Model\OrderFactory $ordercollection,
        \Dotsquares\Gdpr\Helper\Data $helper,
        array $data = []
    ){
        $this->_filterProvider = $filterProvider;
        $this->_helper = $helper;
        $this->_orderFactory = $ordercollection;
        $this->_cookieManager = $cookieManager;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getAction()
    {
        return $this->getUrl('gdpr/index/deleteaccount');
    }
}