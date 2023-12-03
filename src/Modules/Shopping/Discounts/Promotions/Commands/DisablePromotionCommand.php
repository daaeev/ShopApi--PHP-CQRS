<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

class DisablePromotionCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}