<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Seller;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * This class contains seller login functions
 */
class Login extends \Magento\Customer\Controller\AbstractAccount {
    /**
     *
     * @var Session
     */
    protected $session;
    
    /**
     *
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     *
     * @param Context $context            
     * @param Session $customerSession            
     * @param PageFactory $resultPageFactory            
     */
    public function __construct(Context $context, Session $customerSession, PageFactory $resultPageFactory) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct ( $context );
    }
    
    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
        if ($this->session->isLoggedIn ()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect*/
            $resultRedirect = $this->resultRedirectFactory->create ();
            $resultRedirect->setPath ( '*/*' );
            return $resultRedirect;
       
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create ();
        $resultPage->setHeader ( 'Login-Required', 'true' );
        return $resultPage;
    }
}
