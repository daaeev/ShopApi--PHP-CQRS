<?php

namespace Project\Modules\Cart\Adapters;

use Project\Modules\Cart\Entity;
use Project\Common\Product\Currency;
use Project\Common\Product\Availability;
use Project\Modules\Catalogue\Product\Api\ProductApi;
use Project\Modules\Catalogue\Product\Api\DTO\Product as ProductDTO;

class ProductsService
{
    public function __construct(
        private ProductApi $products
    ) {}

    public function resolveCartItem(
        int $product,
        int $quantity,
        Currency $currency,
        ?string $size = null,
        ?string $color = null,
    ): Entity\CartItem {
        $product = $this->products->get($product);
        $this->validateProduct($product, $size, $color);

        return new Entity\CartItem(
            Entity\CartItemId::next(),
            $product->id,
            $product->name,
            $this->retrievePrice($product, $currency),
            $quantity,
            $size,
            $color
        );
    }

    private function validateProduct(
        ProductDTO $product,
        ?string $size,
        ?string $color
    ): void {
        if (
            !$product->active
            || !in_array(
                Availability::from($product->availability),
                Availability::available()
            )
        ) {
            throw new \DomainException('Cant add unavailable product');
        }

        if (!empty($size) && !in_array($size, $product->sizes)) {
            throw new \DomainException('Product id:' . $product->id . ' does not has "' . $size . '" size');
        }

        if (!empty($color) && !in_array($color, $product->colors)) {
            throw new \DomainException('Product id:' . $product->id . ' does not has "' . $color . '" color');
        }
    }

    private function retrievePrice(ProductDTO $product, Currency $currency): float
    {
        $itemPrice = null;

        foreach ($product->prices as $productPrice) {
            if ($productPrice->currency === $currency->value) {
                $itemPrice = $productPrice->price;
            }
        }

        if (null === $itemPrice) {
            throw new \DomainException('Product id:' . $product->id . ' does not have price in ' . $currency->value);
        }

        return $itemPrice;
    }
}