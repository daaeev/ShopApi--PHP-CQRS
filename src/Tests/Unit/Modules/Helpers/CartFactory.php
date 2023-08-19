<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Cart\Entity;
use Project\Modules\Cart\Entity\CartItemId;
use Project\Common\Environment\Client\Client;

trait CartFactory
{
    public function makeCart(
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

    public function generateCart(): Entity\Cart
    {
        $cart = Entity\Cart::instantiate(new Client(md5(rand())));
        $cart->flushEvents();
        return $cart;
    }

    public function makeCartItem(
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

    public function generateCartItem(): Entity\CartItem
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