<?php

namespace Project\Modules\Catalogue\Api\Events\Category;

enum CategoryEvent: string
{
    case CREATED = 'categories.created';
    case UPDATED = 'categories.updated';
    case DELETED = 'categories.deleted';
}
