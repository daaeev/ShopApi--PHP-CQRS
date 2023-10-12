<?php

namespace Project\Modules\Shopping\Cart\Utils;

use Project\Modules\Shopping\Cart\Entity;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Modules\Shopping\Discounts\Promocodes\Utils\PromocodeEntity2DTOConverter as PromocodeEntityConverter;

class CartEntity2DTOConverter
{
    public static function convert(Entity\Cart $entity): DTO\Cart
    {
        return new DTO\Cart(
            $entity->getId()->getId(),
            $entity->getClient(),
            $entity->getCurrency()->value,
            $entity->active(),
            array_map('self::convertCartItem', $entity->getItems()),
            $entity->getTotalPrice(),
            $entity->getPromocode()
                ? PromocodeEntityConverter::convert($entity->getPromocode())
                : null,
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }

    public static function convertCartItem(Entity\CartItem $item): DTO\CartItem
    {
        return new DTO\CartItem(
            $item->getId()->getId(),
            $item->getProduct(),
            $item->getName(),
            $item->getPrice(),
            $item->getQuantity(),
            $item->getSize(),
            $item->getColor()
        );
    }
}