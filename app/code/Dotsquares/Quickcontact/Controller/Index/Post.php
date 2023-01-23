<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dotsquares\Quickcontact\Controller\Index; 

class Post extends \Magento\Framework\App\Action\Action
{
	//const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';
    protected $_resource;
	protected $connection;
	protected $_objectManager;
	protected $_resultPageFactory;
	protected $_storeManager;
	protected $_transportBuilder;
	protected $_inlineTranslation;
	protected $scopeConfig;
	
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection  $resource,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_resource = $resource;
		$this->_objectManager = $objectManager;
		$this->_storeManager = $storeManager;
		$this->_transportBuilder = $transportBuilder;
		$this->_inlineTranslation = $inlineTranslation;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
		$this->_transportBuilder = $transportBuilder;
		$this->inlineTranslation = $inlineTranslation;
		$this->scopeConfig = $scopeConfig;
		$this->storeManager = $storeManager;
		//$this->_escaper = $escaper;
    }
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('*/*/');
            return;
        }
		$name = $post['name'];
		$email = $post['email'];
		$phone = $post['phone'];
		$message = $post['message'];
		$data = array("name" => $name,"email" => $email,"phone" => $phone,"message" => $message);
		$refer = $this->_objectManager->create('Dotsquares\Quickcontact\Model\Items')->setData($data);
		$refer->save();
		$this->inlineTranslation->suspend();
         try {
		   
             $error = false;

            if (!\Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['message']), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                $error = true;
            }
			if (!\Zend_Validate::is(trim($post['phone']), 'NotEmpty')) {
                $error = true;
            }
            if ($error) {
                throw new \Exception();
            }
			$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->storeManager->getStore()->getId());
			$templateVars = array(
								'store' => $this->storeManager->getStore(),
								'name' => $name,
								'email' => $email,
								'phone' => $phone,
								'message'   => $message
							);
			$from = array('email' =>  $email, 'name' =>  $name);
			$this->inlineTranslation->suspend();
			 $to = $this->scopeConfig->getValue('trans_email/ident_general/email');
			
			//die("ll");
			$transport = $this->_transportBuilder->setTemplateIdentifier('email_template')
							->setTemplateOptions($templateOptions)
							->setTemplateVars($templateVars)
							->setFrom($from)
							->addTo($to)
							->getTransport();
			$transport->sendMessage();
			$this->inlineTranslation->resume();
					$this->messageManager->addSuccess(
					__('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
					);
					 $this->_redirect('quickcontact/index');
					return;
			} 
		 catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->_redirect('quickcontact/index');
            return;
        }
    }
}
