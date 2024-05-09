<?php

namespace Project\Modules\Shopping\Cart\Commands;

class UpdateOfferCommand
{
    public function __construct(
        public readonly int $item,
        public readonly string $quantity,
    ) {}
}