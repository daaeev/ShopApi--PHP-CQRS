<?php

namespace Project\Modules\Shopping\Order\Entity\Delivery;

enum DeliveryService: string
{
    case NOVA_POST = 'nova_post';
    case UKR_POST = 'ukr_post';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
