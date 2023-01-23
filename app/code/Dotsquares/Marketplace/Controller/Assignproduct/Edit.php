<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Assignproduct;

class Edit extends \Magento\Framework\App\Action\Action {
    /**
     * Data Manipulation Helper File
     * 
     * @var unknown
     */
    protected $dataHelper;
    /**
     * Function to Construct Data Functions
     * 
     * @param \Magento\Framework\App\Action\Context $context            
     * @param \Dotsquares\Marketplace\Helper\Data $dataHelper            
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Dotsquares\Marketplace\Helper\Data $dataHelper) {
        $this->dataHelper = $dataHelper;
        parent::__construct ( $context );
    }
    
    /**
     * Function to load add assign product layout
     *
     * @return $array
     */
    public function execute() {
        $this->_objectManager->get ( 'Dotsquares\Marketplace\Controller\Assignproduct\Manage' )->checkSellerEnabledorNot ();
    }
}