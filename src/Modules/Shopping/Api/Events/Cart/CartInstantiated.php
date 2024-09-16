<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

class CartInstantiated extends AbstractCartEvent
{
    public function getEventId(): string
    {
        return CartEvent::INSTANTIATED->value;
    }
}