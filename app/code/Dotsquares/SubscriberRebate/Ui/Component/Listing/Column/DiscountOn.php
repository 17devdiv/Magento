<?php


namespace Dotsquares\SubscriberRebate\Ui\Component\Listing\Column;


class DiscountOn implements
    \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Subtotal'), 'value' => \Dotsquares\SubscriberRebate\Model\Program::SUBTOTAL],
            ['label' => __('Grand Total'), 'value' => \Dotsquares\SubscriberRebate\Model\Program::GRANDTOTAL],
//            ['label' => __('Fixed'), 'value' => \Dotsquares\SubscriberRebate\Model\Program::FIXED],
        ];
    }
}