<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Queue;

use Magenest\Salesforce\Model\Queue;
use Magenest\Salesforce\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class Campaign
 * @package Magenest\Salesforce\Controller\Adminhtml\Queue
 */
class Campaign extends \Magenest\Salesforce\Controller\Adminhtml\Queue\AbstractionAction
{
    /** @var string */
    protected $type = Queue::TYPE_CAMPAIGN;

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $ruleCollection = $this->_ruleColFactory->create();
            /** @var \Magenest\Salesforce\Model\Queue $queue */
            $queue = $this->queueFactory->create();
            try {
                $queue->deleteQueueByType($this->type);
                $ruleCollectionArr = [];
                $lastItemId = $ruleCollection->getLastItem()->getId();
                $maxRecord = 5000;
                $count = 0;
                /** @var \Magento\CatalogRule\Model\Rule $rule */
                foreach ($ruleCollection->getItems() as $rule) {
                    $count++;
                    $ruleCollectionArr[] = $queue->enqueue($this->type, $rule->getId());
                    if ($count >= $maxRecord || $rule->getId() == $lastItemId) {
                        $queue->enqueueMultiRecords($ruleCollectionArr);
                        $ruleCollectionArr = [];
                        $count = 0;
                    }
                }
                $this->getResponse()->setBody($this->_json->serialize([
                    'error' => 0,
                    'message' => __('All Campaigns have been added to queue.'),
                ]));
                return;
            } catch (\Exception $e) {
                $this->getResponse()->setBody($this->_json->serialize([
                    'error' => 1,
                    'message' => __('Something went wrong while adding all records to queue. Error: '.$e->getMessage())
                ]));
                return;
            }
        }
        return $this->_redirect('*/*/index');
    }
}
