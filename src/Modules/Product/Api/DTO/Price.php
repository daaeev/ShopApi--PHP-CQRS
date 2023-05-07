<?php

namespace Project\Modules\Product\Api\DTO;

use Project\Modules\Product\Entity\Price\Currency;

class Price
{
    public function __construct(
        public readonly Currency $currency,
        public readonly float $price,
    ) {}
}