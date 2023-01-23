<?php
namespace Dotsquares\Marketplace\Block;

/*
 * Dotsquares Marketplace Sellerblock Block
 */
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\Product;

class Sellerblock extends \Magento\Framework\View\Element\Template
{
    const FLAG_REASON_ENABLE = 1;
    const FLAG_REASON_DISABLE = 0;
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $session;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param Customer                                         $customer
     * @param \Magento\Customer\Model\Session                  $session
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        Customer $customer,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ) {
        $this->Customer = $customer;
        $this->Session = $session;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }

        return $this->_product;
    }
}
