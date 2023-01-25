<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Queue;

use Magenest\Salesforce\Model\Queue;

/**
 * Class Account
 * @package Magenest\Salesforce\Controller\Adminhtml\Queue
 */
class Account extends \Magenest\Salesforce\Controller\Adminhtml\Queue\AbstractionAction
{
    /** @var string */
    protected $type = Queue::TYPE_ACCOUNT;

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
                $lastItemId = $customerCollection->getLastItem()->getId();
                $maxRecord = 5000;
                $count = 0;
                foreach ($customerCollection as $customer) {
                    $count++;
                    $customerCollectionArr[] = $queue->enqueue($this->type, $customer->getId());
                    if ($count >= $maxRecord || $customer->getId() == $lastItemId) {
                        $queue->enqueueMultiRecords($customerCollectionArr);
                        $customerCollectionArr = [];
                        $count = 0;
                    }
                }
                $this->getResponse()->setBody($this->_json->serialize([
                    'error' => 0,
                    'message' => __('All Accounts have been added to queue.')
                ]));
                return;
            } catch (\Exception $e) {
                $this->getResponse()->setBody($this->_json->serialize([
                    'error' => 1,
                    'message' => __('Something went wrong while adding Accounts to queue. Error: '.$e->getMessage())
                ]));
                return;
            }
        }
        return $this->_redirect('*/*/index');
    }
}
