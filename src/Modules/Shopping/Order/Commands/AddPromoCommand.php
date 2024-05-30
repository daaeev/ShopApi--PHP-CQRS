<?php

namespace Project\Modules\Shopping\Order\Commands;

class AddPromoCommand
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $promoId,
    ) {}
}