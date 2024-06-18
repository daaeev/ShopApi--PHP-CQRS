<?php

namespace Project\Modules\Shopping\Api\Events\Orders;

use Project\Modules\Shopping\Order\Entity;
use Project\Modules\Shopping\Api\DTO\Order as DTO;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Utils\OrderEntityToDTOConverter;

class AbstractOrderEvent extends Event
{
    public function __construct(
        private readonly Entity\Order $order,
    ) {}

    public function getDTO(): DTO\Order
    {
        return OrderEntityToDTOConverter::convert($this->order);
    }
}