<?php

namespace Project\Modules\Shopping\Api\DTO;

use Project\Common\Utils\Arrayable;

class Offer implements Arrayable
{
    public function __construct(
        public readonly int $id,
        public readonly string $uuid,
        public readonly int $product,
        public readonly string $name,
        public readonly int $regularPrice,
        public readonly int $price,
        public readonly int $quantity,
        public readonly ?string $size = null,
        public readonly ?string $color = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'product' => $this->product,
            'name' => $this->name,
            'regularPrice' => $this->regularPrice,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'size' => $this->size,
            'color' => $this->color
        ];
    }
}