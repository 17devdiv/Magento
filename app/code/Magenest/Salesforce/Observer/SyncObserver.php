<?php
namespace Magenest\Salesforce\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SyncObserver
 * @package Magenest\Salesforce\Observer
 */
abstract class SyncObserver implements ObserverInterface
{
    protected $type;

    /** @var \Magento\Sales\Api\OrderRepositoryInterface  */
    protected $orderRepository;

    /** @var \Magento\Catalog\Api\ProductRepositoryInterface  */
    protected $productRepository;

    /** @var \Magenest\Salesforce\Model\QueueFactory  */
    protected $queueFactory;

    /** @var \Magenest\Salesforce\Model\ResourceModel\Queue  */
    protected $queueResource;

    /** @var \Magenest\Salesforce\Model\ResourceModel\Queue\CollectionFactory  */
    protected $queueColFactory;

    /** @var \Magenest\Salesforce\Helper\Data  */
    protected $helper;

    /** @var \Magenest\Salesforce\Logger\Logger  */
    protected $logger;

    /** @var \Magenest\Salesforce\Model\Sync\Campaign  */
    protected $_campaign;

    /** @var \Magenest\Salesforce\Model\Sync\Lead */
    protected $_lead;

    /** @var \Magenest\Salesforce\Model\Sync\Contact */
    protected $_contact;

    /** @var \Magenest\Salesforce\Model\Sync\Account */
    protected $_account;

    /** @var \Magenest\Salesforce\Model\Sync\Order */
    protected $_order;

    /**  @var \Magenest\Salesforce\Model\Sync\Opportunity */
    protected $_opportunity;

    /** @var \Magenest\Salesforce\Model\Sync\Product  */
    protected $_product;

    /**
     * SyncObserver constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magenest\Salesforce\Model\QueueFactory $queueFactory
     * @param \Magenest\Salesforce\Model\ResourceModel\Queue $queueResource
     * @param \Magenest\Salesforce\Model\ResourceModel\Queue\CollectionFactory $queueColFactory
     * @param \Magenest\Salesforce\Helper\Data $helper
     * @param \Magenest\Salesforce\Logger\Logger $logger
     * @param \Magenest\Salesforce\Model\Sync\Campaign $campaign
     * @param \Magenest\Salesforce\Model\Sync\Lead $lead
     * @param \Magenest\Salesforce\Model\Sync\Contact $contact
     * @param \Magenest\Salesforce\Model\Sync\Account $account
     * @param \Magenest\Salesforce\Model\Sync\Order $order
     * @param \Magenest\Salesforce\Model\Sync\Opportunity $opportunity
     * @param \Magenest\Salesforce\Model\Sync\Product $product
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magenest\Salesforce\Model\QueueFactory $queueFactory,
        \Magenest\Salesforce\Model\ResourceModel\Queue $queueResource,
        \Magenest\Salesforce\Model\ResourceModel\Queue\CollectionFactory $queueColFactory,
        \Magenest\Salesforce\Helper\Data $helper,
        \Magenest\Salesforce\Logger\Logger $logger,
        \Magenest\Salesforce\Model\Sync\Campaign $campaign,
        \Magenest\Salesforce\Model\Sync\Lead $lead,
        \Magenest\Salesforce\Model\Sync\Contact $contact,
        \Magenest\Salesforce\Model\Sync\Account $account,
        \Magenest\Salesforce\Model\Sync\Order $order,
        \Magenest\Salesforce\Model\Sync\Opportunity $opportunity,
        \Magenest\Salesforce\Model\Sync\Product $product
    ){
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->helper       = $helper;
        $this->logger       = $logger;
        $this->queueFactory = $queueFactory;
        $this->queueResource = $queueResource;
        $this->queueColFactory = $queueColFactory;
        $this->_campaign = $campaign;
        $this->_lead = $lead;
        $this->_contact = $contact;
        $this->_account = $account;
        $this->_order = $order;
        $this->_opportunity = $opportunity;
        $this->_product = $product;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function getEnabledSyncConfig($type = '')
    {
        if ($type) {
            return $this->helper->getEnabledSyncConfig($type);
        }
        return $this->helper->getEnabledSyncConfig($this->type);
    }

    /**
     * @param string $type
     * @return int
     */
    public function getSyncConfigMode($type = '')
    {
        if ($type) {
            return $this->helper->getSyncConfigMode($type);
        }
        return $this->helper->getSyncConfigMode($this->type);
    }

    /**
     * @param $type
     * @return int
     */
    public function getSyncConfigTime($type = '')
    {
        if ($type) {
            return $this->helper->getSyncConfigTime($type);
        }
        return $this->helper->getSyncConfigTime($this->type);
    }

    /**
     * @param string $type
     * @param int $entityId
     * @throws \Exception
     */
    public function addToQueue($type, $entityId)
    {
        /** add to queue mode */
        $queue = $this->queueColFactory->create()
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('entity_id', $entityId)
            ->getFirstItem();
        if ($queue->getId()) {
            $queue->setEnqueueTime(time());
        } else {
            $queue = $this->queueFactory->create();
            $data  = [
                'type' => $type,
                'entity_id' => $entityId,
                'enqueue_time' => time(),
                'priority' => 1,
            ];
            $queue->setData($data);
        }
        $this->queueResource->save($queue);
        return;
    }
}
