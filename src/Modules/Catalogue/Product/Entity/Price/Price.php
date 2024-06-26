<?php

namespace Project\Modules\Catalogue\Product\Entity\Price;

use Project\Common\Product\Currency;

class Price
{
    public function __construct(
        private Currency $currency,
        private int $price
    ) {}

    public function equalsTo(self $other): bool
    {
        return (
            ($other->getCurrency() === $this->getCurrency())
            && ($other->getPrice() === $this->getPrice())
        );
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}