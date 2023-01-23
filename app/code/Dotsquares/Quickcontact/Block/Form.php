<?php
	
namespace Dotsquares\Quickcontact\Block;

class Form extends \Magento\Framework\View\Element\Template
{
	public function _prepareLayout()
	{
	
	return parent::_prepareLayout();
	
	}
	public function getFormAction()
    {
        return $this->getUrl('quickcontact/index/post', ['_secure' => true]);
    }

	
	}

