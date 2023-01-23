<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Seller;

use Dotsquares\Marketplace\Model\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * This class contains validating seller login functions
 */
class LoginPost extends \Magento\Customer\Controller\AbstractAccount {
    /** @var AccountManagementInterface */
    protected $accountManagement;
    
    /** @var Validator */
    protected $formKeyValidator;
    
    /**
     *
     * @var AccountRedirect
     */
    protected $accountRedirect;
    
    /**
     *
     * @var Session
     */
    protected $session;
    
    /**
     *
     * @param Context $context            
     * @param Session $customerSession            
     * @param AccountManagementInterface $customerAccountManagement            
     * @param CustomerUrl $customerHelperData            
     * @param Validator $formKeyValidator            
     * @param AccountRedirect $accountRedirect            
     */
    public function __construct(Context $context, Session $customerSession, AccountManagementInterface $customerAccountManagement, CustomerUrl $customerHelperData, Validator $formKeyValidator, AccountRedirect $accountRedirect) {
        $this->session = $customerSession;
        $this->accountManagement = $customerAccountManagement;
        $this->customerUrl = $customerHelperData;
        $this->formKeyValidator = $formKeyValidator;
        $this->accountRedirect = $accountRedirect;
        parent::__construct ( $context );
    }
    
    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute() {
        if ($this->session->isLoggedIn () || ! $this->formKeyValidator->validate ( $this->getRequest () )) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create ();
            $resultRedirect->setPath ( '*/*/' );
            return $resultRedirect;
        }
        
        if ($this->getRequest ()->isPost ()) {
            $login = $this->getRequest ()->getPost ( 'login' );
            if (! empty ( $login ['username'] ) && ! empty ( $login ['password'] )) {
                try {
                    $customer = $this->accountManagement->authenticate ( $login ['username'], $login ['password'] );
                    $this->session->setCustomerDataAsLoggedIn ( $customer );
                    $this->session->regenerateId ();
                } catch ( EmailNotConfirmedException $e ) {
                    $value = $this->customerUrl->getEmailConfirmationUrl ( $login ['username'] );
                    $message = __ ( 'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.', $value );
                    $this->messageManager->addError ( $message );
                    $this->session->setUsername ( $login ['username'] );
                } catch ( AuthenticationException $e ) {
                    $message = __ ( 'Invalid login or password.' );
                    $this->messageManager->addError ( $message );
                    $this->session->setUsername ( $login ['username'] );
                } catch ( \Exception $e ) {
                    $this->messageManager->addError ( __ ( 'Invalid login or password.' ) );
                }
            } else {
                $this->messageManager->addError ( __ ( 'A login and a password are required.' ) );
            }
        }
        
        return $this->resultRedirectFactory->create()->setPath('marketplace/seller/dashboard/', ['_current' => true]);
    }
}
