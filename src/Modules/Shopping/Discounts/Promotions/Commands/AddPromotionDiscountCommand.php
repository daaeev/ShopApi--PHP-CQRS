<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class AddPromotionDiscountCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $discountType,
        public readonly array $discountData,
    ) {}
}