<?php


namespace Dotsquares\SubscriberRebate\Controller\Index;


class Index extends
    \Dotsquares\SubscriberRebate\Controller\AbstractAction
{

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $collection = $this->programFactory->create()->getCollection();
        foreach ($collection as $item){
            \Zend_Debug::dump($item->getData());
        }
    }
}