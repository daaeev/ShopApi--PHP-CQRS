<?php

namespace Project\Modules\Shopping\Order\Commands;

class RemoveOfferCommand
{
    public function __construct(
        public readonly int|string $id,
        public readonly int|string $offerId,
    ) {}
}