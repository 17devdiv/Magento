<?php

#die("hello");

namespace Dotsquares\Sitemap\Model\System\Config\Source;
use Magento\Framework\Data\OptionSourceInterface;

class Userlinks implements OptionSourceInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {



        return [
            ['value' => 'login', 'label' => __('Login')],
            ['value' => 'register', 'label' => __('Register')],
            ['value' => 'forget', 'label' => __('Forget Password')],
            
        ];






    }

    

}
