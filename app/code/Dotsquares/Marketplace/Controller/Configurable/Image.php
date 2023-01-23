<?php

/**
 * Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     2.0.1
 * @author      Dotsquares Team
 * @copyright   Copyright (c) 2021 Dotsquares. (https://www.dotsquares.com)
 *
 */
namespace Dotsquares\Marketplace\Controller\Configurable;

/**
 * This class used to configurable product image
 */
class Image extends \Magento\Framework\App\Action\Action {
    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactoryObject;
    
    /**
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    public function __construct(\Magento\Framework\View\Result\PageFactory $resultPageFactoryObject, \Magento\Framework\App\Action\Context $context, \Magento\Framework\View\LayoutFactory $layoutFactory) {
        $this->messageManager = $context->getMessageManager();
        $this->resultPageFactoryObject = $resultPageFactoryObject;
        $this->layoutFactory = $layoutFactory;
        parent::__construct ( $context );
    }
    
    /**
     * To create configurable image, price and quantity block
     *
     * @return void
     */
    public function execute() {
        /**
         * To resolving Cannot modify header information issue
         */
        ob_start ();
        return $this->layoutFactory->create ()->createBlock ( 'Dotsquares\Marketplace\Block\Product\Image' )->setTemplate ( 'product/image.phtml' )->toHtml ();
    }
}
