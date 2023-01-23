<?php

namespace Dotsquares\Sortbyoutofstock\Plugin\Block\Widget;

class Tabs 

{

  public function afterAddTab(\Magento\Backend\Block\Widget\Tabs $subject, $result)

  {
     $subject->setActiveTab("order_creditmemos"); // you can set any tab name here

     return $result;

  }

}