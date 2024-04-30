<?php

namespace Project\Modules\Shopping\Utils;

use Project\Modules\Shopping\Entity;
use Project\Modules\Shopping\Api\DTO;

class OfferEntity2DTOConverter
{
    public static function convert(Entity\Offer $offer): DTO\Offer
    {
        return new DTO\Offer(
            $offer->getId()->getId(),
            $offer->getProduct(),
            $offer->getName(),
            $offer->getRegularPrice(),
            $offer->getPrice(),
            $offer->getQuantity(),
            $offer->getSize(),
            $offer->getColor()
        );
    }
}