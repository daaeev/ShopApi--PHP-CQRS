<?php

namespace Project\Tests\Unit\Modules\Cart\Entity\Cart;

use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;

class CalculateCartPriceTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, PromocodeFactory, AssertEvents;

    public function testCalculate()
    {
        $cart = $this->generateCart();
        $cart->addItem($this->generateCartItem());
        $cart->addItem($this->generateCartItem());
        $cart->addItem($this->generateCartItem());
        $cart->usePromocode($this->generatePromocode());

        $totalPrice = array_reduce($cart->getItems(), function ($totalPrice, $item) {
            return $totalPrice + ($item->getPrice() * $item->getQuantity());
        }, 0);

        $totalPrice -= ($totalPrice / 100) * $cart->getPromocode()->getDiscountPercent();
        $this->assertSame($totalPrice, $cart->getTotalPrice());
    }
}