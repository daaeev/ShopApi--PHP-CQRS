<?php

namespace Project\Modules\Product\Api\DTO;

use Project\Common\Utils;

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
            'prices' => array_map(function (Price $price) {
                return $price->toArray();
            }, $this->prices),
            'colors' => array_map(function (Color $color) {
                return $color->toArray();
            }, $this->colors),
            'sizes' => $this->sizes,
        ];
    }
}