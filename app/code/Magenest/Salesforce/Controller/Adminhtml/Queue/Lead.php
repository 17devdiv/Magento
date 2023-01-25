<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Queue;

use Magenest\Salesforce\Model\Queue;
use Magenest\Salesforce\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config;

/**
 * Class Lead
 * @package Magenest\Salesforce\Controller\Adminhtml\Queue
 */
class Lead extends \Magenest\Salesforce\Controller\Adminhtml\Queue\AbstractionAction
{
    /** @var string */
    protected $type = Queue::TYPE_LEAD;

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            /** @var \Magento\Customer\Model\Customer $customerCollection */
            $customerCollection = $this->customerCollection->create();
            /** @var \Magenest\Salesforce\Model\Queue $queue */
            $queue = $this->queueFactory->create();
            try {
                $queue->deleteQueueByType($this->type);
                $customerCollectionArr = [];
                $lastItemId            = $customerCollection->getLastItem()->getId();
                $maxRecord             = 5000;
                $count                 = 0;
                foreach ($customerCollection as $customer) {
                    $customerId = $customer->getId();
                    if (intval($this->getCustomerOrderCollection($customerId)->getSize()) == 0) {
                        $count++;
                        $customerCollectionArr[] = $queue->enqueue($this->type, $customer->getId());
                        if ($count >= $maxRecord || $customerId == $lastItemId) {
                            $queue->enqueueMultiRecords($customerCollectionArr);
                            $customerCollectionArr = [];
                            $count                 = 0;
                        }
                    }
                }
                $this->getResponse()->setBody($this->_json->serialize([
                    'error'   => 0,
                    'message' => __('All Leads have been added to queue.'),
                ]));
                return;
            } catch (\Exception $e) {
                $this->getResponse()->setBody($this->_json->serialize([
                    'error'   => 0,
                    'message' => __('Something went wrong while adding record(s) to queue. Error: ' . $e->getMessage()),
                ]));
                return;
            }
        }
        return $this->_redirect('*/*/index');
    }

    /**
     * @param $customerId
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected function getCustomerOrderCollection($customerId)
    {
        return $this->_helperData->getCustomerOrderCollection($customerId);
    }
}
