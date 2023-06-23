<?php

namespace Project\Modules\Product\Utils;

use Project\Modules\Product\Entity;
use Project\Modules\Product\Api\DTO;

class Entity2DTOConverter
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
                return [
                    'currency' => $price->getCurrency()->value,
                    'value' => $price->getPrice()
                ];
            }, $entity->getPrices()),
        );
    }
}