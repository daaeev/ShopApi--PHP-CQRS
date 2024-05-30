<?php

namespace Project\Modules\Shopping\Order\Commands;

class AddOfferCommand
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $productId,
        public readonly int $quantity,
        public readonly ?string $size = null,
        public readonly ?string $color = null,
    ) {}
}