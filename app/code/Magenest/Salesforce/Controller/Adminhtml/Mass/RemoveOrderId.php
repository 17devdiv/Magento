<?php
namespace Magenest\Salesforce\Controller\Adminhtml\Mass;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class RemoveOrderId extends \Magento\Backend\App\Action
{
    /** @var \Magento\Ui\Component\MassAction\Filter  */
    protected $_filter;

    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory  */
    protected $collectionFactory;

    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $_resource;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * RemoveOrderId constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->orderRepository = $orderRepository;
        $this->_filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_resource = $resource;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collectionFilter = $this->_filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collectionFilter->getSize();
        $count = 0;
        foreach ($collectionFilter as $item) {
            $count++;
            $this->removeAllSFOrderId($item);
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 data have been deleted.', $collectionSize));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order/index');
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function removeAllSFOrderId(\Magento\Sales\Model\Order $order)
    {
        $orderRepository = $this->orderRepository->get($order->getEntityId());
        $orderRepository->setData(\Magenest\Salesforce\Model\Sync\Order::SALESFORCE_ORDER_ATTRIBUTE_CODE, "");
        $this->orderRepository->save($orderRepository);
    }

}