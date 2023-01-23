<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Block\Contact;

use Magento\Framework\View\Element\Template;

/**
 * This class used to display the contact admin form for seller
 */
class Form extends \Magento\Directory\Block\Data {
    
    /**
     * Prepare layout for contact form
     *
     * @return \Magento\Framework\View\Element\AbstractBlock::_prepareLayout()
     */
    public function _prepareLayout() {
        /**
         * Set page title
         */
        $this->pageConfig->getTitle ()->set ( __ ( "Contact Admin" ) );
        /**
         * Call prepare layout
         */
        return parent::_prepareLayout ();
    }
}