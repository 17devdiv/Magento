<?php
/**
 * Dotsquares
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @author Dotsquares
 * @package Dotsquares_GDPR
 * @copyright Copyright (c) Dotsquares (https://www.dotsquares.com/)
 */

namespace Dotsquares\Gdpr\Controller\Index; 

use \Magento\Customer\Model\Session;

class Gdprmail extends \Magento\Framework\App\Action\Action {

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dotsquares\Gdpr\Helper\Data $helper,
		\Magento\Newsletter\Model\SubscriberFactory $newsletterFactory,
        Session $customerSession
    ){
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_messageManager = $context->getMessageManager();
        $this->_helper = $helper;
        $this->_newsletterFactory = $newsletterFactory;
        parent::__construct($context);
    }

    public function execute()
    {
		$othermails = $this->_request->getParam('othermails');
		$customer_id = $this->_customerSession->getCustomerId();
		$newsletter = $this->_newsletterFactory->create()->loadByCustomerId($customer_id);
		try{
			if($newsletter->getId() != ''){
                if((isset($othermails)) && ($othermails == 1)){
                    $newsletter->setOthermails($othermails)->save();
                    $this->_messageManager->addSuccess(__('Thanks for allowing, you will get the newsletters from us.'));
                }else{
                    $newsletter->setOthermails(0)->save();
                    $this->_messageManager->addSuccess(__('You will not get any newsletter from us.'));
                }
                return $this->_redirect('*/*/gdpr/');
			}else{
				$this->_messageManager->addError(__('Please subscribes newsletter mail.'));
				return $this->_redirect('newsletter/manage/');
			}
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __("We can\'t change."));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->_redirect('*/*/gdpr/');
        }
	}
}