<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Seller;

/**
 * This class contains load seller store functions
 */
class Profile extends \Magento\Framework\App\Action\Action {
    /**
     * Marketplace helper data object
     *
     * @var \Dotsquares\Marketplace\Helper\Data
     */
    protected $dataHelper;
    /**
     * Constructor
     *
     * \Dotsquares\Marketplace\Helper\Data $dataHelper
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load seller store page
     *
     * @return $array
     */
    public function execute() {
        /**
         * Check whether module enabled or not
         */
        $moduleEnabled = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabled) {
            $loggingCustomer = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
            $customerId = $loggingCustomer->getId ();
            $sellerObject = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
            $status = $sellerObject->load ( $customerId, 'customer_id' )->getStatus ();
            if ($loggingCustomer->isLoggedIn () && $status == 1) {
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($loggingCustomer->isLoggedIn () && $status == 0) {
                $this->_redirect ( 'marketplace/general/changebuyer' );
            } else {
                $this->messageManager->addNotice ( __ ( 'You must have a seller account to access' ) );
                $this->_redirect ( 'marketplace/seller/login' );
            }
        } else {
            $this->_redirect ( 'customer/account' );
        }
    }
}
