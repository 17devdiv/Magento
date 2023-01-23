<?php

namespace Dotsquares\Productfaq\Controller\Adminhtml\Items;

class NewAction extends \Dotsquares\Productfaq\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
