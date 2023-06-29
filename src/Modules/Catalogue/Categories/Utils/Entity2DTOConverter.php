<?php

namespace Project\Modules\Catalogue\Categories\Utils;

use Project\Modules\Catalogue\Categories\Entity;
use Project\Modules\Catalogue\Api\DTO\Category as DTO;

class Entity2DTOConverter
{
    public static function convert(Entity\Category $entity): DTO\Category
    {
        return new DTO\Category(
            $entity->getId()->getId(),
            $entity->getName(),
            $entity->getSlug(),
            $entity->getProducts(),
            $entity->getParent()->getId(),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }
}