<?php
/**
 * @package   Mediarox_OutOfStockLast
 * @copyright Copyright 2021 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author    Marcus Bernt <mbernt@mediarox.de>
 */

declare(strict_types=1);

namespace Dotsquares\Sortbyoutofstock\Model\Elasticsearch;

class PreventPriceSortFlag
{
    private $preventFlag = false;

    public function enableFlag(): void
    {
        $this->preventFlag = true;
    }

    public function disableFlag(): void
    {
        $this->preventFlag = false;
    }

    public function get(): bool
    {
        return $this->preventFlag;
    }
}
