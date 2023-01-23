<?php


namespace Dotsquares\SubscriberRebate\Block\Totals\Creditmemo;


class NewsletterDiscount extends
    \Magento\Sales\Block\Order\Totals
{
    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo     = $totalsBlock->getCreditmemo();
        $discount    = $creditmemo->getNewsletterDiscount();
        if ( $discount ) {
            $totalsBlock->addTotal(new \Magento\Framework\DataObject(array(
                                                                         'code'        => 'newsletter_discount',
                                                                         'label'       => __('Subscription Discount'),
                                                                         'value'       => -$discount,
                                                                         'is_formated' => false,
                                                                     )), 'subtotal');
        }
    }
}