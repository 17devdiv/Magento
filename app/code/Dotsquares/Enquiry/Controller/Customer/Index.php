<?php
namespace Dotsquares\Enquiry\Controller\Customer;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	public function execute()
	{	 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->get('Magento\Customer\Model\Session');
		if($customerSession->isLoggedIn())
		{
			$this->_view->loadLayout();
			$conf_text_meta_title = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('gernal_section_ds/general/text_meta_title');
			$conf_text_meta_keywords = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('gernal_section_ds/general/text_meta_keywords');
			$conf_text_meta_description = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('gernal_section_ds/general/text_meta_description');
			$resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->set($conf_text_meta_title);
			$resultPage->getConfig()->setKeywords($conf_text_meta_keywords);
			$resultPage->getConfig()->setDescription($conf_text_meta_description);
			$this->_view->renderLayout();
		}
		else
		{	
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
			$resultRedirect->setPath('customer/account/login/');
            return $resultRedirect;			   
		}
	}
  
	public function save() {
		 $anss = $this->getRequest()->getParam();
	}
}