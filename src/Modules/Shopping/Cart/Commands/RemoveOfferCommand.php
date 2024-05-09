<?php

namespace Project\Modules\Shopping\Cart\Commands;

class RemoveOfferCommand
{
    public function __construct(
        public readonly int $item,
    ) {}
}