<?php

namespace Dotsquares\Productfaq\Model\Resourcen\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Dotsquares\Productfaq\Model\Items', 'Dotsquares\Productfaq\Model\Resourcen\Items');
    }
}
