<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Offerbanner\Model\Resource\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dotsquares\Offerbanner\Model\Items', 'Dotsquares\Offerbanner\Model\Resource\Items');
    }
}
