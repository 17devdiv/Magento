<?php

/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Controller\Configurable;

/**
 * This class used to get configurable attribute options
 */
class Summary extends \Magento\Framework\App\Action\Action {

    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultFactory;
    /**
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    public function __construct(\Magento\Framework\View\Result\PageFactory $resultFactory, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Framework\App\Action\Context $context) {
        $this->messageManager = $context->getMessageManager();
        $this->resultedFactory = $resultFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct ( $context );
    }

    /**
     * To create configurable summary content
     *
     * @return void
     */
    public function execute() {
        $resultPage = $this->resultedFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Summary'));
        $block = $resultPage->getLayout()
        ->createBlock('Dotsquares\Marketplace\Block\Product\Summary')
        ->setTemplate('product/summary.phtml')
        ->toHtml();
        $this->getResponse()->setBody($block);
    }
}
