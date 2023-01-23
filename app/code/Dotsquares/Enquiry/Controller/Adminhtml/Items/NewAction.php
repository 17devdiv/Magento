<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Enquiry\Controller\Adminhtml\Items;

class NewAction extends \Dotsquares\Enquiry\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
