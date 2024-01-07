<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

class RefreshPromotionStatusCommand
{
    public function __construct(
        public readonly int $id
    ) {}
}