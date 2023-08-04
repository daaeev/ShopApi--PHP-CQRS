<?php

namespace Project\Modules\Catalogue\Product\Commands;

class CreateProductCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly bool $active,
        public readonly string $availability,
        public readonly array $colors,
        public readonly array $sizes,
        public readonly array $prices,
    ) {}
}