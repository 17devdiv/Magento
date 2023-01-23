<?php

/**
 *
 * @Author              Ngo Quang Cuong <bestearnmoney87@gmail.com>
 * @Date                2016-12-23 18:16:21
 * @Last modified by:   nquangcuong
 * @Last Modified time: 2017-11-28 17:21:05
 */

namespace Dotsquares\Sitemap\Block;
use Dotsquares\Sitemap\Helper\Data as HelperConfig;

 
class Sitemap extends \Magento\Framework\View\Element\Template
{
    protected $_helper;
    protected $_categoryCollection;
    protected $_storeManager;
    protected $_categoryFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        HelperConfig $helper,
        array $data = []
    ) {
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_categoryCollection = $categoryCollection;
        $this->_storeManager = $storeManager;  
        $this->_categoryFactory = $categoryFactory;
        $this->_helper= $helper;

        parent::__construct($context, $data);
    }
 
    /*Get Pages Collection from site. */


  



    
    public function getPages() {

        #echo $this->_helper->isEnablePageSitemap();
        #die("hello");
 #die("monaaa");



        $searchCriteria = $this->searchCriteriaBuilder->create();
        $pages = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();
        return $pages;

    }


   


    public function getCategoryCollection()
    {
        $collection = $this->_categoryCollection->create()
            ->addAttributeToSelect('*')
            ->setStore($this->_storeManager->getStore())
            //->addAttributeToFilter('attribute_code', '1')
            ->addAttributeToFilter('is_active','1');
       return $collection;
    }

    

    
    public function getCategory($categoryId) 
    {
        $category = $this->_categoryFactory->create();
        $category->load($categoryId);
        return $category;
    }

    public function getCategoryProducts($categoryId) 
    {
        $products = $this->getCategory($categoryId)->getProductCollection();
        $products->addAttributeToSelect('*');
        return $products;
    }  




    public function getAdditionLinksCollection()
    {
        $additionLinks = $this->_helper->getAdditionalLinks();
        $allLink       = explode("\n", $additionLinks);

        $result = [];
        foreach ($allLink as $link) {
            if (count($component = explode(',', $link)) > 1) {
                $result[$component[0]] = $component[1];
            }
        }

        /*echo '<pre>';
        print_r($result);
        die();*/

        return $result;
    }



    public function renderSection($config)
    {

        if ($config) {
                    switch ($config) {
                        case 'category':
                            $pagesF = $this->getCategoryCollection();
                            break;
                        case 'pages':

                           $pagesF= $this->getPages();
                              
                                 break;
                        case 'product':
                            $pagesF=true;
                            break;
                        case 'link':
                            $pagesF= $this->getAdditionLinksCollection();
                            break;
                        case 'user_pages':
                            $pagesF= $this->_helper->getCustomerGroupList();
                            break;
                    }   
            
    }
    return $pagesF;
}






    public function renderHtmlSitemap()
    {
  

        if($this->_helper->isEnablePageSitemap())

        {    
             $htmlSitemap =  $this->renderSection('pages');
              return $htmlSitemap;
        }
     
       
    }



 public function renderHtmlSitemap1()
    {
        if($this->_helper->isEnableCategorySitemap())
        {

        $htmlSitemap1 = $this->renderSection('category');
    

        return $htmlSitemap1;}
    }

 public function renderHtmlSitemap12()
    {
        if($this->_helper->isEnableProductSitemap())
        {

        $htmlSitemap1 = $this->renderSection('category');
    

        return $htmlSitemap1;}
    }
     public function renderHtmlSitemap2()
    {
        if($this->_helper->isEnableProductSitemap())
        {

        $htmlSitemap1 = $this->renderSection('product');
    

        return $htmlSitemap1;}
    }

       public function renderHtmlSitemap3()
    {
        if($this->_helper->getAdditionalLinks())
        {
            #die("Adddd");

        $htmlSitemap1 = $this->renderSection('link');
    

        return $htmlSitemap1;}
    }

         public function renderHtmlSitemap4()
    {
        
            #die("Adddd");

        $htmlSitemap4 = $this->renderSection('user_pages');
      /*  echo '<pre>';
    print_r($htmlSitemap1) ;
    die("user links");*/

        return $htmlSitemap4;
    }

    




  
    public function isEnableHtmlSitemap()
    {
        return $this->_helper->isEnableHtmlSiteMap();
        #die('in html');
    }

}