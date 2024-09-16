<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

class CartDeleted extends AbstractCartEvent
{
    public function getEventId(): string
    {
        return CartEvent::DELETED->value;
    }
}