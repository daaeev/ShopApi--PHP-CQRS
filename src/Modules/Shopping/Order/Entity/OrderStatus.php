<?php

namespace Project\Modules\Shopping\Order\Entity;

enum OrderStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case AWAIT_DELIVERY = 'await_delivery';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
