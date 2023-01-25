<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Queue;

use Magenest\Salesforce\Model\Queue;
use Magenest\Salesforce\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Product
 * @package Magenest\Salesforce\Controller\Adminhtml\Queue
 */
class Product extends \Magenest\Salesforce\Controller\Adminhtml\Queue\AbstractionAction
{

    /** @var string */
    protected $type = Queue::TYPE_PRODUCT;
    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $productCollection = $this->_productColFactory->create();
            /** @var \Magenest\Salesforce\Model\Queue $queue */
            $queue = $this->queueFactory->create();
            try {
                $queue->deleteQueueByType($this->type);
                $productCollectionArr = [];
                $lastItemId = $productCollection->getLastItem()->getId();
                $maxRecord = 5000;
                $count = 0;
                foreach ($productCollection->getItems() as $product) {
                    $count++;
                    $productCollectionArr[] = $queue->enqueue($this->type, $product->getId());
                    if ($count >= $maxRecord || $product->getId() == $lastItemId) {
                        $queue->enqueueMultiRecords($productCollectionArr);
                        $productCollectionArr = [];
                        $count = 0;
                    }
                }
                $this->getResponse()->setBody($this->_json->serialize([
                    'error' => 0,
                    'message' => __('All Products have been added to queue.'),
                ]));
                return;
            } catch (\Exception $e) {
                $this->getResponse()->setBody($this->_json->serialize([
                    'error' => 0,
                    'message' => __('Something went wrong while adding record(s) to queue. Error: '.$e->getMessage())
                ]));
                return;
            }
        }
        return $this->_redirect('*/*/index');
    }
}
