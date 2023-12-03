<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

class DeletePromotionCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}