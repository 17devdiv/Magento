<?php
/**
 * Dotsquares
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Dotsquares.com license that is
 * available through the world-wide-web at this URL:
 * https://www.Dotsquares.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Dotsquares
 * @package     Dotsquares_Sitemap
 * @copyright   Copyright (c) Dotsquares (http://www.Dotsquares.com/)
 * @license     https://www.Dotsquares.com/LICENSE.txt
 */

namespace Dotsquares\Sitemap\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;




/**
 * Class Config
 * @package Dotsquares\Sitemap\Helper
 */
class Data extends AbstractHelper
{


    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }


    const CONFIG_MODULE_PATH='seo';
    #const HTML_SITEMAP_CONFIGUARATION = 'html_sitemap/';
    

    /************************ HTML Sitemap Configuration *************************
     * Is enable html site map
     *
     * @param null $storeId
     *
     * @return mixed
     */
   

    /**
     * @param $code
     * @param null $storeId
     *
     * @return array|bool|mixed
     */


    public function getConfigValue($field, $storeId = null)
    {
        #echo $field;
        #die("mona");
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
        
    }


    public function getHtmlSitemapConfig($group,$code, $storeId = null)
    {
        #echo self::CONFIG_MODULE_PATH;

         #die("heyyyy");
        return $this->getConfigValue(
            self::CONFIG_MODULE_PATH . '/' . $group. '/' . $code,
            $storeId
        );
        #die("hlwlwlw");
    }



     public function isEnableHtmlSiteMap($storeId = null)

    {
        #die("hey");
        return $this->getHtmlSitemapConfig('html_sitemap','id11', $storeId);
    }

    public function Getpagetabtitle($storeId = null)
    {
        return $this->getHtmlSitemapConfig('html_sitemap','id13', $storeId);
    }


    /**
     * Is enable Category site map
     * @return mixed
     */
    public function isEnableCategorySitemap()
    {
        return $this->getHtmlSitemapConfig('html_sitemap2','id16');
    }

    /**
     * Is enable page site map
     * @return mixed
     */
    public function isEnablePageSitemap()
    {
        
        return $this->getHtmlSitemapConfig('html_sitemap1','page');
        #die("Yes");
    }

    /**
     * Is enable product site map
     * @return mixed
     */
    public function isEnableProductSitemap()
    {
        return $this->getHtmlSitemapConfig('html_sitemap3','id15');
    }

    

    public function getAdditionalLinks()
    {
        return $this->getHtmlSitemapConfig('html_sitemap4','additional_links');
    }


     public function getCustomerGroupList() {
        $list = $this->scopeConfig->getValue("seo/html_sitemap1/id17",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $list !== null ? explode(',', $list) : [];
    }


    public function getSitemapPageTitle()
    {
        return $this->getHtmlSitemapConfig('html_sitemap','id12');

    }

     public function CatProdDependent()
    {
        return $this->getHtmlSitemapConfig('html_sitemap2','id14');

    }


     public function getPagesTitle()
    {
        return $this->getHtmlSitemapConfig('html_sitemap1','id125');
    }

     public function getCategoryTitle()
    {
        return $this->getHtmlSitemapConfig('html_sitemap2','id126');
    }

     public function getProductTitle()
    {
        return $this->getHtmlSitemapConfig('html_sitemap3','id127');
    }
     public function getADDLinksTitle()
    {
        return $this->getHtmlSitemapConfig('html_sitemap4','id128');
    }

}