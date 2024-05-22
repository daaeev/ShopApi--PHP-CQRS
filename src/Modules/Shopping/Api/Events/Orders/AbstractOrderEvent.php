<?php

namespace Project\Modules\Shopping\Api\Events\Orders;

use Project\Common\Utils;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Utils\OrderEntityToDTOConverter;

class AbstractOrderEvent extends Event
{
    public function __construct(
        private readonly Order $order,
    ) {}

    public function getDTO(): Utils\DTO
    {
        return OrderEntityToDTOConverter::convert($this->order);
    }
}