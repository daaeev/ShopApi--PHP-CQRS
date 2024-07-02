<?php

namespace Project\Modules\Shopping\Order\Entity;

enum OrderStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'inProgress';
    case AWAIT_DELIVERY = 'awaitDelivery';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
