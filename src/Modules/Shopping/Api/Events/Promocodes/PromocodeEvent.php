<?php

namespace Project\Modules\Shopping\Api\Events\Promocodes;

enum PromocodeEvent: string
{
    case CREATED = 'promocodes.created';
    case UPDATED = 'promocodes.updated';
    case DELETED = 'promocodes.deleted';
}
