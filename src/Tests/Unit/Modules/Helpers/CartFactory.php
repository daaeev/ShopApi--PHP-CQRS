<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Environment\Client\Client;
use Project\Modules\Shopping\Cart\Entity\CartItemId;

trait CartFactory
{
    private function makeCart(
        Entity\CartId $id,
        Client $client,
        array $items = []
    ): Entity\Cart {
        return new Entity\Cart(
            $id,
            $client,
            $items
        );
    }

    private function generateCart(): Entity\Cart
    {
        $cart = Entity\Cart::instantiate(new Client(md5(rand(1, 9999)), rand(1, 9999)));
        $cart->flushEvents();
        return $cart;
    }

    private function makeCartItem(
        CartItemId $id,
        int $product,
        string $name,
        float $price,
        int $quantity,
        ?string $size = null,
        ?string $color = null,
    ): Entity\CartItem {
        return new Entity\CartItem(
            $id,
            $product,
            $name,
            $price,
            $quantity,
            $size,
            $color,
        );
    }

    private function generateCartItem(): Entity\CartItem
    {
        return new Entity\CartItem(
            Entity\CartItemId::random(),
            rand(1, 9999),
            md5(rand()),
            rand(100, 500),
            rand(1, 9999),
            md5(rand()),
            md5(rand()),
        );
    }
}