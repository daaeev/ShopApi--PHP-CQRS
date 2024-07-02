<?php

namespace Project\Modules\Shopping\Order\Entity;

enum PaymentStatus: string
{
    case PAID = 'paid';
    case NOT_PAID = 'notPaid';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
