<?php


namespace Dotsquares\SubscriberRebate\Model\ResourceModel\Program;


class Collection extends
    \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Dotsquares\SubscriberRebate\Model\Program', 'Dotsquares\SubscriberRebate\Model\ResourceModel\Program');
    }
}