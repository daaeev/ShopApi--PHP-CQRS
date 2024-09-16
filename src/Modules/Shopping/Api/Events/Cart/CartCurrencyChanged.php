<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

class CartCurrencyChanged extends AbstractCartEvent
{
    public function getEventId(): string
    {
        return CartEvent::CURRENCY_CHANGED->value;
    }
}