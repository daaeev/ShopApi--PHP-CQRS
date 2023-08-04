<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Product\Currency;
use Project\Modules\Catalogue\Product\Entity\Product;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Product\Entity\Price\Price;

trait ProductFactory
{
    private function makePrices(): array
    {
        $prices = [];

        foreach (Currency::active() as $activeCurrency) {
            $prices[] = new Price(
                $activeCurrency,
                rand(100, 500)
            );
        }

        return $prices;
    }

    private function makeProduct(
        ProductId $id,
        string $name,
        string $code,
        array $prices = []
    ): Product {
        return new Product(
            $id,
            $name,
            $code,
            $prices
        );
    }

    private function generateProduct(): Product
    {
        $product = new Product(
            ProductId::random(),
            md5(rand()),
            md5(rand()),
            $this->makePrices()
        );

        $product->flushEvents();
        return $product;
    }
}