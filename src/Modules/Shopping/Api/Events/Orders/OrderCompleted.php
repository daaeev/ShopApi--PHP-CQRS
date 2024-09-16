<?php

namespace Project\Modules\Shopping\Api\Events\Orders;

class OrderCompleted extends AbstractOrderEvent
{
    public function getEventId(): string
    {
        return OrderEvent::COMPLETED->value;
    }
}