<?php

namespace Project\Modules\Cart\Utils;

use Project\Modules\Cart\Entity;
use Project\Modules\Cart\Api\DTO;

class Entity2DTOConverter
{
    public static function convert(Entity\Cart $entity): DTO\Cart
    {
        return new DTO\Cart(
            $entity->getId()->getId(),
            $entity->getClient()->getHash(),
            array_map('self::convertCartItem', $entity->getItems()),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }

    private static function convertCartItem(Entity\CartItem $item): DTO\CartItem
    {
        return new DTO\CartItem(
            $item->getProduct(),
            $item->getName(),
            $item->getPrice(),
            $item->getQuantity(),
            $item->getSize(),
            $item->getColor()
        );
    }
}