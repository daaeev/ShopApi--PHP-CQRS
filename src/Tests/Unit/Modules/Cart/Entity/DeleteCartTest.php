<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Shopping\Api\Events\Cart\CartDeleted;

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