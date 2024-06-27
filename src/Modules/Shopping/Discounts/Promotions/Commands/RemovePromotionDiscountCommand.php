<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class RemovePromotionDiscountCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $promotionId,
        public readonly int $discountId,
    ) {}
}