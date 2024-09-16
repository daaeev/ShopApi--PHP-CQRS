<?php

namespace Project\Modules\Catalogue\Api\Events\Category;

class CategoryUpdated extends AbstractCategoryEvent
{
    public function getEventId(): string
    {
        return CategoryEvent::UPDATED->value;
    }
}