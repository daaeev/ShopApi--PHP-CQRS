<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Utils;

use Project\Modules\Shopping\Discounts\Promotions\Entity;
use Project\Modules\Shopping\Discounts\Promotions\Entity\DiscountMechanics;
use Project\Modules\Shopping\Api\DTO\Promotions as DTO;

class PromotionEntity2DTOConverter
{
    public static function convert(Entity\Promotion $entity): DTO\Promotion
    {
        return new DTO\Promotion(
            $entity->getId()->getId(),
            $entity->getName(),
            $entity->getStartDate(),
            $entity->getEndDate(),
            $entity->getActualStatus()->value,
            array_map(function (DiscountMechanics\AbstractDiscountMechanic $discount) {
                return $discount->toArray();
            }, $entity->getDiscounts()),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }
}