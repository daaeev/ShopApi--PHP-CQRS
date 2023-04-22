<?php

namespace Project\Modules\Product\Api\DTO;

use Project\Common\Utils;
use Project\Modules\Product\Entity\ProductId;

class Product implements Utils\DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly bool $active,
        public readonly string $availability,
        public readonly array $colors,
        public readonly array $sizes,
        public readonly array $prices,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'active' => $this->active,
            'availability' => $this->availability,
            'prices' => $this->prices,
            'colors' => $this->colors,
            'sizes' => $this->sizes,
        ];
    }
}