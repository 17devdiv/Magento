<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Queue;

use Magenest\Salesforce\Model\Queue;
use Magenest\Salesforce\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Order
 * @package Magenest\Salesforce\Controller\Adminhtml\Sync
 */
class Order extends \Magenest\Salesforce\Controller\Adminhtml\Queue\AbstractionAction
{
    /** @var string */
    protected $type = Queue::TYPE_ORDER;

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            /** @var \Magento\Customer\Model\Customer $customerCollection */
            $orderCollection = $this->_orderColFactory->create();
            /** @var \Magenest\Salesforce\Model\Queue $queue */
            $queue = $this->queueFactory->create();
            try {
                $queue->deleteQueueByType($this->type);
                $orderCollectionArr = [];
                $lastItemId = $orderCollection->getLastItem()->getIncrementId();
                $maxRecord = 5000;
                $count = 0;
                foreach ($orderCollection->getItems() as $order) {
                    $count++;
                    $orderCollectionArr[] = $queue->enqueue($this->type, $order->getIncrementId());
                    if ($count >= $maxRecord || $order->getIncrementId() == $lastItemId) {
                        $queue->enqueueMultiRecords($orderCollectionArr);
                        $orderCollectionArr = [];
                        $count = 0;
                    }
                }
                $this->getResponse()->setBody($this->_json->serialize([
                    'error' => 0,
                    'message' => __('All Orders have been added to queue.')
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
