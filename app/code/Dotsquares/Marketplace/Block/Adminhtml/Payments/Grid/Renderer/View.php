<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Adminhtml\Payments\Grid\Renderer;

/**
 * This class used to renderer all payments
 */
class View extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action {
    /**
     * Renders column
     *
     * @param \Magento\Framework\DataObject $row            
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row) {
        /**
         * Getting customer id by row
         */
        $customerId = $this->_getValue ( $row );
        /**
         * Get edit url
         */
        $url = $this->getUrl ( '*/allpayments/index/id/' . $customerId );
        $html = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $collection = $objectManager->get ( 'Dotsquares\Marketplace\Model\Payments' )->getCollection ();
        $collection->addFieldToFilter ( 'seller_id', array (
                'eq' => $customerId 
        ) );
        if (isset( $collection )) {
            /**
             * Prepare html content
             */
            $html = $html . '<a href="' . $url . '" >' . __ ( 'View All Payments' ) . '</a>';
        } else {
            $html = $html . 'NA';
        }
        /**
         * Return html content
         */
        return $html;
    }
}