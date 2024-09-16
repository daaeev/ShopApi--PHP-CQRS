<?php

namespace Project\Modules\Shopping\Api\Events\Orders;

class OrderDeleted extends AbstractOrderEvent
{
    public function getEventId(): string
    {
        return OrderEvent::DELETED->value;
    }
}