<?php

namespace Project\Modules\Shopping\Api\Events\Orders;

class OrderCreated extends AbstractOrderEvent
{
    public function getEventId(): string
    {
        return OrderEvent::CREATED->value;
    }
}