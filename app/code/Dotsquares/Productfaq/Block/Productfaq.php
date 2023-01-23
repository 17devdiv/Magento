<?php

namespace Dotsquares\Productfaq\Block;

use Dotsquares\Productfaq\Model\ItemsFactory;

class Productfaq extends \Magento\Framework\View\Element\Template
{
    protected $context;
    protected $_stockItemRepository;
    protected $questionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ItemsFactory $questionFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockItemRepository,
        array $data = []
    ) {
        $this->_questionFactory = $questionFactory;
        $this->_stockItemRepository = $stockItemRepository;
        parent::__construct($context, $data);
    }

    public function getcurrentproduct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');
        return $product->getId();
    }

    public function getQuestionCollection()
    {
        if ($this->getRequest()->getParam('limit')) {
            $limit = $this->getRequest()->getParam('limit');
        } else {
            $limit = 1;
        }
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 2;
        $product_id = $this->getcurrentproduct();
        $collection = $this->_questionFactory->create()->getCollection();
        $collection->addFieldToFilter('product_id', $product_id);
        $collection->addFieldToFilter('status', 1);
        if ($this->getRequest()->getParam('limit') !=  'All') {
            $collection->getSelect()->limit($limit);
        }
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Questions'));
        if ($this->getQuestionCollection()) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'fme.news.pager')->setAvailableLimit(array(2=>2,4=>4,6=>6))->setShowPerPage(true)->setCollection($this->getQuestionCollection());
            $this->setChild('pager', $pager);
            $this->getQuestionCollection()->load();
        }
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
