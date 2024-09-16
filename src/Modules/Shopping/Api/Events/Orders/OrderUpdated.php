<?php

namespace Project\Modules\Shopping\Api\Events\Orders;

class OrderUpdated extends AbstractOrderEvent
{
    public function getEventId(): string
    {
        return OrderEvent::UPDATED->value;
    }
}