<?php

namespace Project\Modules\Shopping\Api\Events\Cart;

enum CartEvent: string
{
    case INSTANTIATED = 'carts.instantiated';
    case UPDATED = 'carts.updated';
    case DELETED = 'carts.deleted';
    case CURRENCY_CHANGED = 'carts.currencyChanged';
    case PROMO_ADDED = 'carts.promoAdded';
    case PROMO_REMOVED = 'carts.promoRemoved';
}
