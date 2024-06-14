<?php

namespace Project\Modules\Catalogue\Api\DTO\Product;

use Project\Common\Utils\Arrayable;

class Price implements Arrayable
{
    public function __construct(
        public readonly string $currency,
        public readonly int $price,
    ) {}

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'price' => $this->price
        ];
    }
}