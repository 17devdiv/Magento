<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Assignproduct;

/**
 * This class contains search assign product functions
 */
class Search extends \Magento\Framework\App\Action\Action {
    protected $dataHelper;
    /**
     * Constructor
     * 
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Dotsquares\Marketplace\Helper\Data $dataHelper            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load assign products layout
     *
     * @return $array
     */
    public function execute() {
        $marketplaceSeller = $this->_objectManager->get ( 'Magento\Customer\Model\Session' );
        $marketplaceSellerId = $marketplaceSeller->getId ();
        $seller = $this->_objectManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
        $marketplacesellerStatus = $seller->load ( $marketplaceSellerId, 'customer_id' )->getStatus ();
        /**
         * Checking whether module enable or not
         */
        $moduleEnabledOrNot = $this->dataHelper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            if ($marketplaceSeller->isLoggedIn () && $marketplacesellerStatus == 1) {
                $storeName = $seller->getStoreName ();
                if (empty ( $storeName )) {
                    $this->messageManager->addNotice ( __ ( 'You must have a seller store to assign products' ) );
                    $this->_redirect ( 'marketplace/seller/profile' );
                }
                $this->_view->loadLayout ();
                $this->_view->renderLayout ();
            } elseif ($marketplaceSeller->isLoggedIn () && $marketplacesellerStatus == 0) {
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