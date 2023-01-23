<?php
/**
 * @package   Mediarox_OutOfStockLast
 * @copyright Copyright 2021 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author    Marcus Bernt <mbernt@mediarox.de>
 */

declare(strict_types=1);

namespace Dotsquares\Sortbyoutofstock\Plugin\Catalog\Model\ResourceModel\Product\Collection\ProductLimitation;

use Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitation;
use Dotsquares\Sortbyoutofstock\Model\Elasticsearch\PreventPriceSortFlag;

class PreventPriceSortPlugin
{
    protected $priceSortFlag;

    public function __construct(PreventPriceSortFlag $priceSortFlag)
    {
        $this->priceSortFlag = $priceSortFlag;
    }

    public function afterIsUsingPriceIndex(ProductLimitation $subject, bool $result)
    {
        if ($this->priceSortFlag->get()) {
            $result = false;
        }

        return $result;
    }
}
