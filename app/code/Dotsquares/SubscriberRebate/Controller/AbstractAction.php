<?php


namespace Dotsquares\SubscriberRebate\Controller;


abstract class AbstractAction extends
    \Magento\Framework\App\Action\Action
{
    /**
     * @var \Dotsquares\SubscriberRebate\Model\ProgramFactory
     */
    protected $programFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dotsquares\SubscriberRebate\Model\ProgramFactory $programFactory
    ) {
        parent::__construct($context);
        $this->programFactory = $programFactory;
    }

}