<?php

namespace Project\Modules\Catalogue\Product\Utils;

use Project\Modules\Catalogue\Product\Entity;
use Project\Modules\Catalogue\Api\DTO\Product as DTO;

class ProductEntity2DTOConverter
{
    public static function convert(Entity\Product $entity): DTO\Product
    {
        return new DTO\Product(
            $entity->getId()->getId(),
            $entity->getName(),
            $entity->getCode(),
            $entity->isActive(),
            $entity->getAvailability()->value,
            $entity->getColors(),
            $entity->getSizes(),
            array_map(function (Entity\Price\Price $price) {
                return new DTO\Price(
                    $price->getCurrency()->value,
                    $price->getPrice()
                );
            }, $entity->getPrices()),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt()
        );
    }
}