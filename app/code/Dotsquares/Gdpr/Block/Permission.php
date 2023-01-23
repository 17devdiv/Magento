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

class Permission extends \Magento\Framework\View\Element\Template
{
    protected $templateProcessor;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        array $data = []
    ){
        $this->_filterProvider = $filterProvider;
        $this->_cookieManager = $cookieManager;
        parent::__construct($context, $data);
    }
    
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function filterOutputHtml($value) 
    {
        $html = $this->_filterProvider->getPageFilter()->filter($value);
        return $html;
    }
    
    public function getCookie()
    {
        return $cookieValue = $this->_cookieManager->getCookie('gdpr_cookies');
    }
	
    public function getAction()
    {
        return $this->getUrl('gdpr/index/gdprmail');
    }
	
    public function getGdprurl()
    {
        return $this->getUrl('gdpr/index/gdpr');
    }
}