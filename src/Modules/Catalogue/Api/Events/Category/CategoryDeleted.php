<?php

namespace Project\Modules\Catalogue\Api\Events\Category;

class CategoryDeleted extends AbstractCategoryEvent
{
    public function getEventId(): string
    {
        return CategoryEvent::DELETED->value;
    }
}