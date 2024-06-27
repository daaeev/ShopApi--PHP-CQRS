<?php

namespace Project\Modules\Shopping\Cart\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class AddOfferCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $product,
        public readonly int $quantity,
        public readonly ?string $size = null,
        public readonly ?string $color = null,
    ) {}
}