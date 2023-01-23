<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Quickcontact\Controller\Adminhtml\Items;

class NewAction extends \Dotsquares\Quickcontact\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
