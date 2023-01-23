<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml\Subscriptionprofiles\Grid\Renderer;

/**
 * Class for seller subscription profile email functions
 */
class SellerEmail extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        $email = '';
        $id = $this->_getValue ( $row );
        /**
         * Checking for id avaiable or not
         */
        if ($id != '') {
            /**
             * Creating customer instance
             */
            $customerObj = \Magento\Framework\App\ObjectManager::getInstance ();
            /**
             * Getting seller email id
             */
            $sellerObj = $customerObj->get ( 'Magento\Customer\Model\Customer' )->load ( $id );
            $email = $sellerObj->getEmail ();
        }
        /**
         * Return seller email value
         */
        return $email;
    }
}