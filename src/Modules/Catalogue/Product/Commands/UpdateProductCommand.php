<?php

namespace Project\Modules\Catalogue\Product\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class UpdateProductCommand implements ApplicationMessageInterface
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
}