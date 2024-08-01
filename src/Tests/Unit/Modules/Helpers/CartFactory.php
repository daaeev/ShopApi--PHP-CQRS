<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Modules\Shopping\Cart\Entity;
use Project\Common\Services\Environment\Client;

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
			new Client(hash: uniqid(), id: rand(1, 9999))
		);

        $cart->flushEvents();
        return $cart;
    }
}