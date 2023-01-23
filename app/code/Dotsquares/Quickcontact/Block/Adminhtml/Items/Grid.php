<?php

namespace Dotsquares\Quickcontact\Block\Adminhtml\Items;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_quickcontactcollection;
	
	protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Dotsquares\Quickcontact\Model\ResourceModel\Items\CollectionFactory $quickCollectionFactory,
    
        array $data = []
    ) {
        $this->_quickcontactcollection = $quickCollectionFactory;
        $this->_objectManager = $objectManager;

        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('itemsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
		$this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
		
        $collection = $this->_objectManager->create('Dotsquares/Quickcontact/Model/ResourceModel/Items')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('email')
			->addAttributeToSelect('phone')
            ->addAttributeToSelect('message');

		

        
        //$collection = $this->_rewardpointscollection->create();

        $this->setCollection($collection);
		
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
		
		$this->addColumn(
            'firstname',
            [
                'header' => __('Name'),
                'type' => 'text',
                'index' => 'name',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
		$this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'type' => 'text',
                'index' => 'email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
		 $this->addColumn(
            'phone',
            [
                'header' => __('Phone'),
                'type' => 'text',
                'index' => 'phone',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
		$this->addColumn(
            'message',
            [
                'header' => __('Message'),
                'type' => 'text',
                'index' => 'message',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        return parent::_prepareColumns();
    }

    
    
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

  }
