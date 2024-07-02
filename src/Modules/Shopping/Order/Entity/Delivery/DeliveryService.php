<?php

namespace Project\Modules\Shopping\Order\Entity\Delivery;

enum DeliveryService: string
{
    case NOVA_POST = 'novaPost';
    case UKR_POST = 'ukrPost';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
