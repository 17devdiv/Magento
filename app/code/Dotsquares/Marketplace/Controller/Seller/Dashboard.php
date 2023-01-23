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
 * This class contains seller dashboard functions
 */
class Dashboard extends \Magento\Framework\App\Action\Action {
    /**
     * Marketplace helper data object
     *
     * @var \Dotsquares\Marketplace\Helper\Data
     */
    protected $dataHelper;
    
    /**
     * Constructor
     *
     * @param \Dotsquares\Marketplace\Helper\Data $dataHelper            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load recent orders in seller Dashboard
     *
     * @return $array
     */
    public function execute() {
        $customerObject = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerObject->getId ();
        $sellerObject = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        if(!empty($customerId)){
        $status = $sellerObject->load ( $customerId, 'customer_id' )->getStatus ();
        $this->checkingForModuleEnabledOrNOt ( $status, $customerObject );
        }else{
        $this->_redirect ( 'marketplace/seller/login' );
        }
    }
    /**
     * Function to check whether seller or not
     *
     * @return layout
     */
    public function checkingForModuleEnabledOrNOt($status, $customerObject) {
        /**
         * Check whether module enabled or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            if ($customerObject->isLoggedIn () && $status == 1) {
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customerObject->isLoggedIn () && $status == 0) {
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
