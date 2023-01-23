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

class Gdpr extends \Magento\Framework\App\Action\Action {
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Dotsquares\Gdpr\Helper\Data $helper,
        Session $customerSession
    )
    {
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $enable = $this->_helper->getConfig('cusotmerdelete/gdprlaw/gdpr_enable');
        $customerSession = $this->_customerSession->get('Magento\Customer\Model\Session');
        $customerId = $this->_customerSession->getCustomerId();
        if($enable == 1 && !empty($customerId) && $customerId != '' && $customerId != null) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('GDPR Policy'));
            return $resultPage;
        }else{
            $this->_redirect('customer/account/login/');
        }
    }
}