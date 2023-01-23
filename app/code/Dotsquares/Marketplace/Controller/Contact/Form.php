<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Contact;

/**
 * This class contains contact seller form
 */
class Form extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var \Dotsquares\Marketplace\Helper\Data
     */
    protected $dataHelper;
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
        /**
         * Get logged in customer details
         */
        $customerSession = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $customerId = $customerSession->getId ();
        $sellerModel = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
        $this->checkSellerOrNot ( $status, $customerSession );
    }
    /**
     * Function to check whether seller or not
     *
     * @param int $status            
     * @param object $customerSession            
     *
     * @return layout
     */
    public function checkSellerOrNot($status, $customerSession) {
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        /**
         * Check whether module enabled or not
         */
        if ($moduleEnabledOrNot) {
            if ($customerSession->isLoggedIn () && $status == 1) {
                /**
                 * Load layout
                 */
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($customerSession->isLoggedIn () && $status == 0) {
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
