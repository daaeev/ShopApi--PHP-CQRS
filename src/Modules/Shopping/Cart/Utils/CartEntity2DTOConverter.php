<?php

namespace Project\Modules\Shopping\Cart\Utils;

use Project\Modules\Shopping\Api\DTO;
use Project\Modules\Shopping\Entity\Offer;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Utils\OfferEntity2DTOConverter;
use Project\Modules\Shopping\Discounts\Promocodes\Utils\PromocodeEntity2DTOConverter as PromocodeEntityConverter;

class CartEntity2DTOConverter
{
    public static function convert(Cart $entity): DTO\Cart\Cart
    {
        return new DTO\Cart\Cart(
            $entity->getId()->getId(),
            $entity->getClient(),
            $entity->getCurrency()->value,
            array_map(fn (Offer $offer) => OfferEntity2DTOConverter::convert($offer), $entity->getOffers()),
            $entity->getTotalPrice(),
            $entity->getPromocode()
                ? PromocodeEntityConverter::convert($entity->getPromocode())
                : null,
            $entity->getCreatedAt(),
            $entity->getUpdatedAt(),
        );
    }
}