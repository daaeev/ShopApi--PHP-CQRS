<?php

namespace Project\Modules\Catalogue\Api\Events\Product;

enum ProductEvent: string
{
    case CREATED = 'products.created';
    case UPDATED = 'products.updated';
    case DELETED = 'products.deleted';
    case ACTIVITY_CHANGED = 'products.activityChanged';
    case AVAILABILITY_CHANGED = 'products.availabilityChanged';
    case CODE_CHANGED = 'products.codeChanged';
    case PRICES_CHANGED = 'products.pricesChanged';
}
