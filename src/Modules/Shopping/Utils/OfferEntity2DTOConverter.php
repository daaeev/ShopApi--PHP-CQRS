<?php

namespace Project\Modules\Shopping\Utils;

use Project\Modules\Shopping\Offers;
use Project\Modules\Shopping\Api\DTO;

class OfferEntity2DTOConverter
{
    public static function convert(Offers\Offer $offer): DTO\Offer
    {
        return new DTO\Offer(
            $offer->getId()->getId(),
            $offer->getUuid()->getId(),
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