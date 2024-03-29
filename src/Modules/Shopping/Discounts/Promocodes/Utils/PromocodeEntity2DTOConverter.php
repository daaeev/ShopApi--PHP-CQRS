<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Utils;

use Project\Modules\Shopping\Discounts\Promocodes\Entity;
use Project\Modules\Shopping\Api\DTO\Promocodes as DTO;

class PromocodeEntity2DTOConverter
{
    public static function convert(Entity\Promocode $entity): DTO\Promocode
    {
        return new DTO\Promocode(
            $entity->getId()->getId(),
            $entity->getName(),
            $entity->getCode(),
            $entity->getDiscountPercent(),
            $entity->getActive(),
            $entity->getStartDate(),
            $entity->getEndDate(),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }
}