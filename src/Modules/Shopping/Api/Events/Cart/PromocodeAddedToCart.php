<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

class PromocodeAddedToCart extends AbstractCartEvent
{
    public function getEventId(): string
    {
        return CartEvent::PROMO_ADDED->value;
    }
}