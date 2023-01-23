<?php

namespace Dotsquares\Productfaq\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Result\PageFactory;

class Productdelete implements ObserverInterface
{
    protected function getwriteConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_write');
        }
        return $this->connection;
    }

    protected function getreadConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection('core_read');
        }
        return $this->connection;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $product = $observer->getEvent()->getProduct();
        $product_data = $product->getStockData();
        $product_id = $product->getId();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('dotsquares_productfaq_items');
        $delete = 'DELETE FROM '.$tableName.' WHERE product_id = '.$product_id;
        $connection->query($delete);
    }
}
