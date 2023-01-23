<?php
/**

 *
 * @category    Dotsquares
 * @package     Dotsquares_Marketplace
 * @version     3.5.2

 *
 */
namespace Dotsquares\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Dotsquares\Marketplace\Helper\Data;

/**
 * This class contains seller approval/disapproval functions
 */
class Shipment implements ObserverInterface {
    /**
     *
     * @var $marketplaceData
     */
    protected $marketplaceData;

    /**
     * Constructor
     *
     * @param Data $marketplaceData
     */
    public function __construct(Data $marketplaceData) {
        $this->marketplaceData = $marketplaceData;
    }
    /**
     * Execute the result
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $shipment = $observer->getEvent ()->getShipment ();
        $order = $shipment->getOrder ();
        $invoice = $order->canInvoice ();
        if ($invoice) {
            $orderStatus = 'processing';
        } else {
            $orderStatus = 'completed';
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerOrderCollection = $objectManager->get ( 'Dotsquares\Marketplace\Model\Order' )->getCollection ()->addFieldToFilter ( 'order_id', $order->getId () );
        $sellerOrderCollectionDatas = $sellerOrderCollection->getData ();
        foreach ( $sellerOrderCollectionDatas as $sellerOrderCollectionData ) {
            $objectManager->get ( 'Dotsquares\Marketplace\Model\Order' )->load ( $sellerOrderCollectionData ['id'] )->setStatus($orderStatus)->save();
        }
    }
}