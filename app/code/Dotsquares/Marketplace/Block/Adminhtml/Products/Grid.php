<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml\Products;

/**
 * Class For Manage Products Grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
    /**
     *
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    /**
     *
     * @var \Dotsquares\Grid\Model\GridFactory
     */
    protected $_gridFactory;
    /**
     *
     * @var \Dotsquares\Grid\Model\Status
     */
    protected $_status;
    /**
     * Initialize constructor
     * @param \Magento\Backend\Block\Template\Context $context            
     * @param \Magento\Backend\Helper\Data $backendHelper @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @return void
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Catalog\Model\ProductFactory $gridFactory, \Dotsquares\Marketplace\Model\System\Config\Status $status, \Magento\Framework\Module\Manager $moduleManager, array $data = []) {
        $this->_gridFactory = $gridFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct ( $context, $backendHelper, $data );
    }
    /**
     * Constructor function
     * @return void
     */
    protected function _construct() {
        parent::_construct ();
        $this->setId ( 'productsGrid' );
        $this->setDefaultSort ( 'entity_id' );
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
        $this->setUseAjax ( true );
        $this->setVarNameFilter ( 'grid_record' );
    }
    /**
     * Prepare Collection
     * 
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_gridFactory->create ()->getCollection ();
        $collection->addAttributeToSelect ( '*' );
        $collection->addAttributeToFilter ( 'seller_id', array (
                'notnull' => true 
        ) );
        $this->setCollection ( $collection );
        parent::_prepareCollection ();
        return $this;
    }
    /**
     * Function for Mass Action
     * 
     * @return object
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField ( 'entity_id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'id' );
        $this->getMassactionBlock ()->addItem ( 'Approve', [ 
                'label' => __ ( 'Approve' ),
                'url' => $this->getUrl ( 'marketplaceadmin/products/massapprove' ) 
        ] );
        $this->getMassactionBlock ()->addItem ( 'Disapprove', [ 
                'label' => __ ( 'Disapprove' ),
                'url' => $this->getUrl ( 'marketplaceadmin/products/massdisapprove' ) 
        ] );
        return $this;
    }
    /**
     *Function to prepare columns
     * @return object
     */
    protected function _prepareColumns() {
        $this->addColumn ( 'entity_id', [ 
                'header' => __ ( 'ID' ),
                'type' => 'number',
                'filter' => false,
                'sortable' => false,
                'index' => 'entity_id' 
        ] );
        $this->addColumn ( 'name', [ 
                'header' => __ ( 'Name' ),
                'type' => 'text',
                'index' => 'name' 
        ] );
        $this->addColumn ( 'sku', [ 
                'header' => __ ( 'Sku' ),
                'type' => 'text',
                'index' => 'sku' 
        ] );
        $this->addColumn ( 'price', [ 
                'header' => __ ( 'Price' ),
                'type' => 'text',
                'index' => 'price' 
        ] );
        $this->addColumn ( 'product_approval', array (
                'header' => __ ( 'Approval Status' ),
                'index' => 'product_approval',
                'type' => 'text',
                'renderer' => '\Dotsquares\Marketplace\Block\Adminhtml\Products\Grid\Renderer\Status' 
        ) );
        
        $this->addColumn ( 'product_status', array (
                'header' => __ ( 'Status' ),
                'index' => 'status',
                'type' => 'text',
                'renderer' => '\Dotsquares\Marketplace\Block\Adminhtml\Products\Grid\Renderer\ProductStatus' 
        ) );
        $this->addColumn ( 'seller_id', [ 
                'header' => __ ( 'Seller' ),
                'type' => 'text',
                'index' => 'seller_id',
                'filter' => false,
                'sortable' => false,
                'renderer' => '\Dotsquares\Marketplace\Block\Adminhtml\Seller\Grid\Renderer\Name' 
        ] );
        $this->addColumn ( 'email', array (
                'header' => __ ( 'Seller Email' ),
                'index' => 'seller_id',
                'type' => 'email',
                'filter' => false,
                'sortable' => false,
                'renderer' => '\Dotsquares\Marketplace\Block\Adminhtml\Products\Grid\Renderer\SellerEmail' 
        ) );
        
        $this->addColumn ( 'action', array (
                'header' => __ ( 'Action' ),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                        array (
                                'caption' => __ ( 'Edit' ),
                                'url' => array (
                                        'base' => 'catalog/product/edit',
                                        'params' => array (
                                                'store' => $this->getRequest ()->getParam ( 'store' ) 
                                        ) 
                                ),
                                'field' => 'id' 
                        ) 
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores' 
        ) );
        
        $this->addColumn ( 'product_preview', array (
                'header' => __ ( 'Preview' ),
                'index' => 'entity_id',
                'type' => 'text',
                'renderer' => '\Dotsquares\Marketplace\Block\Adminhtml\Products\Grid\Renderer\Productpreview' 
        ) );
        
        $block = $this->getLayout ()->getBlock ( 'grid.bottom.links' );
        if ($block) {
            $this->setChild ( 'grid.bottom.links', $block );
        }
        
        return parent::_prepareColumns ();
    }
}