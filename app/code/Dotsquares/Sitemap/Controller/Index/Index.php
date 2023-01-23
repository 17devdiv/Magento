<?php

/**
 *
 * @Author              Ngo Quang Cuong <bestearnmoney87@gmail.com>
 * @Date                2016-12-23 16:58:08
 * @Last modified by:   nquangcuong
 * @Last Modified time: 2016-12-27 16:30:33
 */

namespace Dotsquares\Sitemap\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Dotsquares\Sitemap\Helper\Data as HelperConfig;

class Index extends \Magento\Framework\App\Action\Action
{
    
     protected $pageFactory;

    
    protected $helperConfig;

    
    public function __construct(
        Context $context, PageFactory $pageFactory, HelperConfig $helperConfig,\Magento\Framework\App\ViewInterface $_view
    ) {
        $this->pageFactory  = $pageFactory;
        $this->helperConfig = $helperConfig;

        return parent::__construct($context);
    }

    /**
     * View action
     *
     * @return \Magento\Framework\View\Result\PageFactory
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if (!$this->helperConfig->isEnableHtmlSiteMap()) {
            throw new NotFoundException(__('Parameter is incorrect.'));
        }

        $title=$this->helperConfig->Getpagetabtitle();
        $this->_view->getPage()->getConfig()->getTitle()->set(__($title));
        #die("in index");
        
       
        #die("innnddeexxx");

        $page = $this->pageFactory->create();
        
        #$page->getConfig()->getTitle()->set(__('HTML Sitemap'));
        #echo $page;
        #die("index");

        return $page;
    }
}
