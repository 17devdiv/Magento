<?php
/**
 * Copyright © 2015 Dotsquares. All rights reserved.
 */

namespace Dotsquares\Offerbanner\Controller\Adminhtml\Items;

class NewAction extends \Dotsquares\Offerbanner\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
