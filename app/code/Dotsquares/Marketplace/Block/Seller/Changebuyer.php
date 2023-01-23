<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Seller;

class Changebuyer extends \Magento\Framework\View\Element\Template {

    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set ( __ ( 'Become a Seller' ) );
        return parent::_prepareLayout ();
    }
}