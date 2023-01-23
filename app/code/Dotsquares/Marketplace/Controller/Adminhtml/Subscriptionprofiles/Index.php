<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Adminhtml\Subscriptionprofiles;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action {
    /**
     *
     * @var PageFactory
     */
    protected $viewresult;
    
    /**
     *
     * @param Context $context            
     * @param PageFactory $viewresult            
     */
    public function __construct(Context $context, PageFactory $viewresult) {
        parent::__construct ( $context );
        $this->viewresult = $viewresult;
    }
    
    /**
     * Index action
     *
     * @return void
     */
    public function execute() {
        /**
         * Create view result for subscription profiles page
         */
        $viewResult = $this->viewresult->create ();
        /**
         * To activate marektplace menu
         */
        $viewResult->setActiveMenu ( 'Dotsquares_Marketplace::manage_subscriptionprofiles' );
        /**
         * Add breadcrumb for subscribed profiles
         */
        $viewResult->addBreadcrumb ( __ ( 'Subscription Profiles' ), __ ( 'Subscribed Profiles' ) );
        /**
         * Setting title for subscripbed profiles
         */
        $viewResult->getConfig ()->getTitle ()->prepend ( __ ( 'Subscribed Profiles' ) );
        /**
         * Return page
         */
        return $viewResult;
    }
}
