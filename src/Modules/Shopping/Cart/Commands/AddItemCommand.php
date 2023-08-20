<?php

namespace Project\Modules\Shopping\Cart\Commands;

class AddItemCommand
{
    public function __construct(
        public readonly int $product,
        public readonly int $quantity,
        public readonly ?string $size = null,
        public readonly ?string $color = null,
    ) {}
}