<?php

namespace Project\Modules\Shopping\Api\Events\Promotions;

enum PromotionEvent: string
{
    case CREATED = 'promotions.created';
    case UPDATED = 'promotions.updated';
    case DELETED = 'promotions.deleted';
}
