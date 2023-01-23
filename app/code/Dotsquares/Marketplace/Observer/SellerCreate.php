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
class SellerCreate implements ObserverInterface {
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
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     *
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;
    
    /**
     *
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;
    protected $redirect;
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
        $this->_session = $session;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->_customerUrl = $customerUrl;
        $this->redirect = $redirect;
    }
    /**
     * Execute the result
     * 
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $formId = 'seller_create';
        $captcha = $this->_helper->getCaptcha ( $formId );
        if ($captcha->isRequired ()) {
            /** @var \Magento\Framework\App\Action\Action $controller */
            $controller = $observer->getControllerAction ();
            
            if (! $captcha->isCorrect ( $this->captchaStringResolver->resolve ( $controller->getRequest (), $formId ) )) {
                $this->messageManager->addError ( __ ( 'Incorrect CAPTCHA.' ) );
                $this->_actionFlag->set ( '', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true );
                $this->redirect->redirect ( $controller->getResponse (), 'marketplace/seller/login' );
            }
        }
    }
}