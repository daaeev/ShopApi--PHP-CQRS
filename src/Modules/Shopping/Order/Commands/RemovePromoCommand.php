<?php

namespace Project\Modules\Shopping\Order\Commands;

class RemovePromoCommand
{
    public function __construct(
        public readonly int|string $id,
    ) {}
}