<?php

namespace Magenest\Salesforce\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class MagentoLink
 * @package Magenest\Salesforce\Ui\Component\Listing\Columns
 */
class MagentoLink extends Column
{
    /** @var UrlInterface  */
    protected $urlBuilder;

    /**
     * MagentoLink constructor.
     *
     * @param UrlInterface $urlInterface
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlInterface,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [])
    {
        $this->urlBuilder = $urlInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['magento_id']) {
                    $path = '';
                    $param = 'id';
                    switch ($item['salesforce_table']) {
                        case 'Account':
                        case 'Contact':
                        case 'Lead':
                            $path = 'customer/index/edit/';
                            break;
                        case 'Order':
                        case 'Opportunity':
                            $path = 'sales/order/view/';
                            $param = 'order_id';
                            break;
                        case 'Product2':
                        case 'OrderItem':
                        case 'OpportunityLineItem':
                        case 'PricebookEntry':
                            $path = 'catalog/product/edit/';
                            break;
                        case 'Campaign':
                            $path = 'catalog_rule/promo_catalog/edit/';
                            break;
                    }
                    $additionalTable = [
                        'SHIPPING',
                        'TAX',
                        'CATEGORY'
                    ];
                    if(!in_array($item['magento_id'],$additionalTable)){
                        $item['magento_id'] = '<a href="' . $this->urlBuilder->getUrl($path, [$param => $item['magento_id']]) . '" target="_blank">'.$item['magento_id'].'</a>';
                    }
                }
            }
        }
        return $dataSource; // TODO: Change the autogenerated stub
    }
}