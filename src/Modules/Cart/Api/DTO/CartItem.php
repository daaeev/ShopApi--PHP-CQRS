<?php

namespace Project\Modules\Cart\Api\DTO;

use Project\Common\Utils\Arrayable;

class CartItem implements Arrayable
{
    public function __construct(
        public readonly int|string $product,
        public readonly string $name,
        public readonly float $price,
        public readonly int $quantity,
        public readonly ?string $size = null,
        public readonly ?string $color = null,
    ) {}

    public function toArray(): array
    {
        return [
            'product' => $this->product,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'size' => $this->size,
            'color' => $this->color
        ];
    }
}