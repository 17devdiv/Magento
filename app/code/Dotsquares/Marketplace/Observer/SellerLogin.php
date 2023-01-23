<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Dotsquares\Marketplace\Helper\Data;

/**
 * This class contains seller approval/disapproval functions
 */
class SellerLogin implements ObserverInterface {
    /**
     *
     * @var $marketplaceData
     */
    protected $_helper;
    
    /**
     *
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
    /**
     *
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     *
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $redirect;
    protected $_session;
    /**
     * Customer data
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;
    
    /**
     * Constructor
     * 
     * @param Data $marketplaceData            
     */
    public function __construct(\Magento\Captcha\Helper\Data $helper, \Magento\Framework\App\ActionFlag $actionFlag, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\Session\SessionManagerInterface $session, CaptchaStringResolver $captchaStringResolver, \Magento\Customer\Model\Url $customerUrl, \Magento\Framework\App\Response\RedirectInterface $redirect) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->_customerUrl = $customerUrl;
        $this->redirect = $redirect;
        $this->_session = $session;
        $this->captchaStringResolver = $captchaStringResolver;
    }
    /**
     * Execute the result
     * 
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $formId = 'seller_login';
        $captcha = $this->_helper->getCaptcha ( $formId );
        if ($captcha->isRequired ()) {
            /** @var \Magento\Framework\App\Action\Action $controller */
            $controller = $observer->getControllerAction ();
            
            if (! $captcha->isCorrect ( $this->captchaStringResolver->resolve ( $controller->getRequest (), $formId ) )) {
                $this->_actionFlag->set ( '', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true );
                $this->messageManager->addError ( __ ( 'Incorrect CAPTCHA.' ) );
                $this->redirect->redirect ( $controller->getResponse (), 'marketplace/seller/login' );
            }
        }
    }
}