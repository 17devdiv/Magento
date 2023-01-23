<?php 
namespace Dotsquares\Offerbanner\Controller\Index; 

class Index extends \Magento\Framework\App\Action\Action {
	protected $catalogSession;
    protected $resultPageFactory;
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Catalog\Model\Session $catalogSession)     {
        $this->resultPageFactory = $resultPageFactory;
		$this->catalogSession = $catalogSession;
        parent::__construct($context);
    }
	public function execute()
    {
		//echo "hello";
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend(__('Offerbanner'));
		return $resultPage;
    }
	
}