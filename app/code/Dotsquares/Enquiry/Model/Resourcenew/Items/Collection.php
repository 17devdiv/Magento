<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Enquiry\Model\Resourcenew\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dotsquares\Enquiry\Model\Items', 'Dotsquares\Enquiry\Model\Resourcenew\Items');
    }
}
