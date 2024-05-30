<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Common\Client\Client;
use Project\Common\Product\Currency;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Cart\Entity\CartId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Shopping\Api\Events\Cart\CartDeleted;
use Project\Modules\Shopping\Api\Events\Cart\CartInstantiated;

class DeleteCartTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, AssertEvents;

    public function testDelete()
    {
        $cart = $this->generateCart();
        $cart->delete();
        $this->assertEvents($cart, [new CartDeleted($cart)]);
    }
}