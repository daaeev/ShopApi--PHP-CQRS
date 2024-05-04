<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Shopping\Entity;

trait OffersFactory
{
    private function makeOffer(
        Entity\OfferId $id,
        int $product = 1,
        string $name = 'Offer',
        float $regularPrice = 500,
        float $price = 400,
        int $quantity = 1,
        ?string $size = null,
        ?string $color = null,
    ): Entity\Offer {
        return new Entity\Offer(
            id: $id,
            product: $product,
            name: $name,
			regularPrice: $regularPrice,
            price: $price,
            quantity: $quantity,
            size: $size,
            color: $color,
        );
    }

    private function generateOffer(): Entity\Offer
    {
        return new Entity\Offer(
            id: Entity\OfferId::random(),
            product: rand(1, 9999),
            name: md5(rand()),
            regularPrice: rand(400, 500),
            price: rand(100, 400),
            quantity: rand(1, 9999),
            size: md5(rand()),
            color: md5(rand()),
        );
    }
}