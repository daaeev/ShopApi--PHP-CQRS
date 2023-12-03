<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
