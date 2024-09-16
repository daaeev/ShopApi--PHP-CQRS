<?php

namespace Project\Modules\Catalogue\Api\Events\Category;

use Project\Modules\Catalogue\Categories\Entity;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Catalogue\Categories\Utils\CategoryEntity2DTOConverter;

abstract class AbstractCategoryEvent extends Event
{
    public function __construct(
        private readonly Entity\Category $entity
    ) {}

    public function getData(): array
    {
        return CategoryEntity2DTOConverter::convert($this->entity)->toArray();
    }
}