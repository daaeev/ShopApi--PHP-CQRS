<?php

namespace Project\Modules\Shopping\Api\Events\Orders;

use Project\Modules\Shopping\Order\Entity;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Order\Utils\OrderEntityToDTOConverter;

abstract class AbstractOrderEvent extends Event
{
    public function __construct(
        private readonly Entity\Order $order,
    ) {}

    public function getData(): array
    {
        return OrderEntityToDTOConverter::convert($this->order)->toArray();
    }
}