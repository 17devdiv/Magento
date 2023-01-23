<?php
namespace Dotsquares\Enquiry\Controller\Customer;
use Magento\Framework\Controller\ResultFactory;
use Magento\TestFramework\ErrorLog\Logger;

class Filldata extends \Magento\Framework\App\Action\Action {
	
    protected $_resource;
	protected $_transportBuilder;
	protected $_inlineTranslation;
	protected $scopeConfig;
	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\ResourceConnection  $resource,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder

    ) {
        
		$this->_resource = $resource;
		$this->_transportBuilder = $transportBuilder;
		parent::__construct($context);
    }
   
    public function execute()
    {
		$data = $this->getRequest()->getParams();		
		if (!$data) {
            $this->_redirect('*/*/');
            return;
        }
		
		try{			
			$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
			$ScopeConfigInterface = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
			$currentStore = $storeManager->getStore(); 
			$storeId = $storeManager->getStore()->getId();
			
			$question = $this->_objectManager->create('Dotsquares\Enquiry\Model\Items');
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['enabled'] = 1;
			$customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($data['client_id']);
			
			/*email*/
			$name = $customer->getName();
			$email = $customer->getEmail();
			$phone = $customer->getTelephone();
			$enquiry_content = $data['enquiry_content'];
			$dotsquares_number = $data['dotsquares_number'];
			$reason_for_enquiry = $data['name'];
			
			$error = false;

            if (!\Zend_Validate::is(trim($name), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($enquiry_content), 'NotEmpty')) {
                $error = true;
            }
            if (!\Zend_Validate::is(trim($email), 'EmailAddress')) {
                $error = true;
            }
			if (!\Zend_Validate::is(trim($dotsquares_number), 'NotEmpty')) {
                $error = true;
            }
			if (!\Zend_Validate::is(trim($reason_for_enquiry), 'NotEmpty')) {
                $error = true;
            }
			
            if ($error) {
                throw new \Exception();
            }

            //Admin mail section
			$subject_admin_mail = $ScopeConfigInterface->getValue('gernal_section_ds/enquiry_template/text_subject');
			$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId);
			$templateVars = array(
				'store' => $currentStore,
				'name' => $name,
				'email' => $email,
				'phone' => $phone,
				'enquiry_content'   => $enquiry_content,
				'dotsquares_number' => $dotsquares_number,
				'reason_for_enquiry' => $reason_for_enquiry
			);
			
			$to_admin_mail = $ScopeConfigInterface->getValue('gernal_section_ds/enquiry_template/text_owner_email_id');

			if(isset($to_admin_mail)) {
				$from_admin_mail = array('email' =>  $email, 'name' =>  $name);
				$templateVars += ['subject' => $subject_admin_mail];

				$transport = $this->_transportBuilder->setTemplateIdentifier('gernal_section_ds_enquiry_template_dropdown_owner_template')
								->setTemplateOptions($templateOptions)
								->setTemplateVars($templateVars)
								->setFrom($from_admin_mail)
								->addTo($to_admin_mail)
								->getTransport(); 
														
				$transport->sendMessage();
			}

			//Customer mail section
			$subject_cstmr_mail = $ScopeConfigInterface->getValue('gernal_section_ds/enquiry_template_customer/text_subject_customer');
			$to_cstmr_mail = $email;
			$support_email = $ScopeConfigInterface->getValue('trans_email/ident_support/email');
			$support_name = $ScopeConfigInterface->getValue('trans_email/ident_support/name');

			if(isset($to_cstmr_mail)) {
				$from_cstmr_mail = array('email' => $support_email, 'name' => $support_name);
				if(isset($templateVars['subject'])) {
					$templateVars['subject'] = $subject_cstmr_mail;
				} else {
					$templateVars += ['subject' => $subject_cstmr_mail];
				}
				$templateVars += ['order_no' => $dotsquares_number];

				$transport = $this->_transportBuilder->setTemplateIdentifier('gernal_section_ds_enquiry_template_customer_dropdown_customer_template')
								->setTemplateOptions($templateOptions)
								->setTemplateVars($templateVars)
								->setFrom($from_cstmr_mail)
								->addTo($to_cstmr_mail)
								->getTransport();
				
				$transport->sendMessage();
			}		
			/*email end*/
			
			$question->setData($data);
			$question->save();
			$sucessmessage = $ScopeConfigInterface->getValue('gernal_section_ds/general/text_success_message');
			if(isset($sucessmessage)) {
				echo " ";
				$this->messageManager->addSuccess( __($sucessmessage)."it is saved in db" );
			} else {
				$this->messageManager->addSuccess( __('Enquiry for order is submitted.') );
			}

		    $this->_redirect('*/*/');
            //return;
		}catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
			$this->_redirect('*/*/');
		}
	}
}