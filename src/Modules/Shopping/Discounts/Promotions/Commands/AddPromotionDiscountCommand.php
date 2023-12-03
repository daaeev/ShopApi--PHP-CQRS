<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

class AddPromotionDiscountCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $discountType,
        public readonly array $discountData,
    ) {}
}