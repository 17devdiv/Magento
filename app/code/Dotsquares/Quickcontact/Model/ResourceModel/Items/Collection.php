<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Quickcontact\Model\ResourceModel\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dotsquares\Quickcontact\Model\Items', 'Dotsquares\Quickcontact\Model\ResourceModel\Items');
    }
}
