<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

class RemovePromotionDiscountCommand
{
    public function __construct(
        public readonly int $promotionId,
        public readonly int $discountId,
    ) {}
}