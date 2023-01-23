<?php


namespace Dotsquares\SubscriberRebate\Ui\Component\Listing\Column;


class DiscountType implements
    \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['label' => __('Fixed'), 'value' => \Dotsquares\SubscriberRebate\Model\Program::FIXED],
            ['label' => __('Percentage'), 'value' => \Dotsquares\SubscriberRebate\Model\Program::PERCENT],
        ];
    }
}