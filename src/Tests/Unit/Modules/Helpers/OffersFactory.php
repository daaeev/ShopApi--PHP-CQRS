<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Shopping\Offers;
use Project\Modules\Shopping\Entity\Promocode;

trait OffersFactory
{
    private function makeOffer(
        Offers\OfferId $id,
        Offers\OfferUuId $uuid,
        int $product = 1,
        string $name = 'Offer',
        int $regularPrice = 500,
        int $price = 400,
        int $quantity = 1,
        ?string $size = null,
        ?string $color = null,
    ): Offers\Offer {
        return new Offers\Offer(
            id: $id,
            uuid: $uuid,
            product: $product,
            name: $name,
			regularPrice: $regularPrice,
            price: $price,
            quantity: $quantity,
            size: $size,
            color: $color,
        );
    }

    private function generateOffer(): Offers\Offer
    {
        return new Offers\Offer(
            id: Offers\OfferId::random(),
            uuid: Offers\OfferUuId::random(),
            product: rand(1, 9999),
            name: uniqid(),
            regularPrice: rand(400, 500),
            price: rand(100, 400),
            quantity: rand(1, 9999),
            size: uniqid(),
            color: uniqid(),
        );
    }

    private function calculateTotalPrice(array $offers, ?Promocode $promocode = null): int
    {
        $totalPrice = array_reduce($offers, function ($totalPrice, $item) {
            return $totalPrice + ($item->getPrice() * $item->getQuantity());
        }, 0);

        if (null !== $promocode) {
            $totalPrice -= ($totalPrice / 100) * $promocode->getDiscountPercent();
        }

        return (int) $totalPrice;
    }

    private function calculateRegularPrice(array $offers): int
    {
        return array_reduce($offers, function ($totalPrice, $item) {
            return $totalPrice + ($item->getRegularPrice() * $item->getQuantity());
        }, 0);
    }
}