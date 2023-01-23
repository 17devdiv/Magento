<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Seller;

/**
 * This class used to display the products collection
 */
class Link extends \Magento\Framework\View\Element\Html\Link {
    protected $_template = 'Dotsquares_Marketplace::account/link.phtml';
    
    /**
     * Function to Get Href for Top Link
     *
     * @return string
     */
    public function getHref() {
        /**
         * Checking whether customer logged in or not
         */
        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $helper = $objectGroupManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
        $moduleEnabledOrNot = $helper->getModuleEnable ();
        if ($moduleEnabledOrNot) {
            $customerSession = $objectGroupManager->get ( 'Magento\Customer\Model\Session' );
            $customerId = $customerSession->getId ();
            $sellerModel = $objectGroupManager->get ( 'Dotsquares\Marketplace\Model\Seller' );
            $status = $sellerModel->load ( $customerId, 'customer_id' )->getStatus ();
            /**
             * Checked customer access level
             */
            
            $returnUrl = '';
            if ($customerSession->isLoggedIn () && $status == 1) {
                $returnUrl = $this->getUrl ( 'marketplace/seller/dashboard' );
            } elseif ($customerSession->isLoggedIn () && $status == 0) {
                $returnUrl = $this->getUrl ( 'marketplace/general/changebuyer' );
            } else {
                $returnUrl = $this->getUrl ( 'marketplace/seller/login' );
            }
            return $returnUrl;
        } else {
            return '#';
        }
    }
    
    /**
     * Function to Get Label on Top Link
     *
     * @return string
     */
    public function getLabel() {
        $objectGroupManager = \Magento\Framework\App\ObjectManager::getInstance ();
            $helper = $objectGroupManager->get ( 'Dotsquares\Marketplace\Helper\Data' );
            $moduleEnabledOrNot = $helper->getModuleEnable ();
            if ($moduleEnabledOrNot) {
                return __ ( 'Sell On ' );
            
        }
    }
}