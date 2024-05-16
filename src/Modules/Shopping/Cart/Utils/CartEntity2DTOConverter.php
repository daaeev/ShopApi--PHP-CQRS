<?php

namespace Project\Modules\Shopping\Cart\Utils;

use Project\Modules\Shopping\Api\DTO\Promocode;
use Project\Modules\Shopping\Api\DTO\Cart as DTO;
use Project\Modules\Shopping\Offers\Offer;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Utils\OfferEntity2DTOConverter;

class CartEntity2DTOConverter
{
    public static function convert(Cart $entity): DTO\Cart
    {
        return new DTO\Cart(
            $entity->getId()->getId(),
            $entity->getClient(),
            $entity->getCurrency()->value,
            array_map(fn (Offer $offer) => OfferEntity2DTOConverter::convert($offer), $entity->getOffers()),
            $entity->getTotalPrice(),
            $entity->getRegularPrice(),
            $entity->getPromocode()
                ? new Promocode(
                    $entity->getPromocode()->getId()->getId(),
                    $entity->getPromocode()->getCode(),
                    $entity->getPromocode()->getDiscountPercent(),
                )
                : null,
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }
}