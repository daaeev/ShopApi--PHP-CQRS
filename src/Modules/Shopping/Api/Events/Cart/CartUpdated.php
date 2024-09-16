<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

class CartUpdated extends AbstractCartEvent
{
    public function getEventId(): string
    {
        return CartEvent::UPDATED->value;
    }
}