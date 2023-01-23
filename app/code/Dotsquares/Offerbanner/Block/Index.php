<?php
namespace Dotsquares\Offerbanner\Block;

use Dotsquares\Offerbanner\Model\ItemsFactory;

class Index extends \Magento\Framework\View\Element\Template
{
	protected $connection;
    protected $resource; 
	protected $_blogFactory;
	
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		ItemsFactory $blogFactory,
		\Magento\Framework\App\ResourceConnection $resource,
        array $data = []
	)
	{
		$this->_resource = $resource;
		$this->_blogFactory = $blogFactory;
		parent::__construct($context, $data);
	}
	
	public function getBlogCollection()
	{
		$collection = $this->_blogFactory->create()->getCollection();
		return $collection->getData();
		
	}
	
	public function getImageMediaPath(){
    	return $this->getUrl('pub/media/slider',['_secure' => $this->getRequest()->isSecure()]);
    }
	
	public function getConnection()
    {
        $connection = $this->_resource->getConnection('core_write');
        return $connection;
    }
}
