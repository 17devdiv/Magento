<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2
 * @author      Dotsquares Team <developers@dotsquares.com>
 * @copyright   Copyright (c) 2016 Dotsquares. (http://www.dotsquares.com)
 * @license     http://www.dotsquares.com/LICENSE.txt
 *
 */
namespace Dotsquares\Marketplace\Block;
/**
 * This class used to configurable product image
 */
class Logincustomer extends \Magento\Framework\View\Element\Template {
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
    }

    public function LoginCustomerDetails()
    {
      return $this->customerSession->getCustomer();
    }
}
