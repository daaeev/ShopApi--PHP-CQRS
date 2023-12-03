<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

class EnablePromotionCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}