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
class Managesellers extends \Magento\Framework\View\Element\Html\Link {
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
            return $this->getUrl ( 'marketplace/seller/allseller' );
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
            return __ ( 'Our Sellers' );
        }
    }
}