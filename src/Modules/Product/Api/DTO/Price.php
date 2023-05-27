<?php

namespace Project\Modules\Product\Api\DTO;

use Project\Common\Utils\Arrayable;

class Price implements Arrayable
{
    public function __construct(
        public readonly string $currency,
        public readonly float $price,
    ) {}

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'price' => $this->price
        ];
    }
}