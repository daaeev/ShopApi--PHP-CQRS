<?php

namespace Project\Modules\Shopping\Order\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class AddOfferCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $productId,
        public readonly int $quantity,
        public readonly ?string $size = null,
        public readonly ?string $color = null,
    ) {}
}