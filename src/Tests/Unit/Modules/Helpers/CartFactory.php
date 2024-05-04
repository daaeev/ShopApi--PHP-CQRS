<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Client\Client;
use Project\Modules\Shopping\Cart\Entity;

trait CartFactory
{
    private function makeCart(Entity\CartId $id, Client $client): Entity\Cart
    {
        return new Entity\Cart($id, $client);
    }

    private function generateCart(): Entity\Cart
    {
        $cart = new Entity\Cart(
			Entity\CartId::random(),
			new Client(hash: md5(rand(1, 9999)), id: rand(1, 9999))
		);

        $cart->flushEvents();
        return $cart;
    }
}