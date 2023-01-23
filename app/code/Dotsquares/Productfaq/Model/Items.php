<?php

namespace Dotsquares\Productfaq\Model;

class Items extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Dotsquares\Productfaq\Model\Resourcen\Items');
    }
}
