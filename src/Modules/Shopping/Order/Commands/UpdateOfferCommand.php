<?php

namespace Project\Modules\Shopping\Order\Commands;

class UpdateOfferCommand
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $offerId,
        public readonly string $quantity,
    ) {}
}