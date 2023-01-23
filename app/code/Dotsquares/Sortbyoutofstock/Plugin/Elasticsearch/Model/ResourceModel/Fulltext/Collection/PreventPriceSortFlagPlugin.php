<?php
/**
 * @package   Mediarox_OutOfStockLast
 * @copyright Copyright 2021 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author    Marcus Bernt <mbernt@mediarox.de>
 */

declare(strict_types=1);

namespace Dotsquares\Sortbyoutofstock\Plugin\Elasticsearch\Model\ResourceModel\Fulltext\Collection;

use Magento\Elasticsearch\Model\ResourceModel\Fulltext\Collection\SearchResultApplier;
use Dotsquares\Sortbyoutofstock\Model\Elasticsearch\PreventPriceSortFlag;

class PreventPriceSortFlagPlugin
{
    protected $priceSortFlag;

    public function __construct(PreventPriceSortFlag $priceSortFlag)
    {
        $this->priceSortFlag = $priceSortFlag;
    }

    public function aroundApply(SearchResultApplier $subject, callable $proceed)
    {
        $this->priceSortFlag->enableFlag();
        $proceed();
        $this->priceSortFlag->disableFlag();
    }
}
