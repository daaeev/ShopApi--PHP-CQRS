<?php

namespace Project\Modules\Cart\Commands;

class AddItemCommand
{
    public function __construct(
        public readonly int $product,
        public readonly int $quantity,
        public readonly ?string $size = null,
        public readonly ?string $color = null,
    ) {}
}