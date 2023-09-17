<?php

namespace Project\Modules\Catalogue\Api\Events\Category;

use Project\Common\Events\Event;
use Project\Modules\Catalogue\Categories\Entity;
use Project\Modules\Catalogue\Api\DTO\Category as DTO;
use Project\Modules\Catalogue\Categories\Utils\CategoryEntity2DTOConverter;

class AbstractCategoryEvent extends Event
{
    public function __construct(
        private Entity\Category $entity
    ) {}

    public function getDTO(): DTO\Category
    {
        return CategoryEntity2DTOConverter::convert($this->entity);
    }
}