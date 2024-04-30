<?php

namespace Project\Modules\Shopping\Order\Entity\Delivery;

enum DeliveryService: string
{
    case NOVA_POST = 'nova_post';
    case UKR_POST = 'ukr_post';
    case WORLD_WIDE = 'world_wide';
}
