<?php

namespace Dotsquares\Productfaq\Model\Resourcen;

class Items extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('dotsquares_productfaq_items', 'id');
    }
}
