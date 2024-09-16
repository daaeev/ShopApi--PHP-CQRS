<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

class PromocodeRemovedFromCart extends AbstractCartEvent
{
    public function getEventId(): string
    {
        return CartEvent::PROMO_REMOVED->value;
    }
}