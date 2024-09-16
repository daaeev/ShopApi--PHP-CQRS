<?php

namespace Project\Modules\Catalogue\Api\Events\Category;

class CategoryCreated extends AbstractCategoryEvent
{
    public function getEventId(): string
    {
        return CategoryEvent::CREATED->value;
    }
}