<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;

class CalculateCartPriceTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, OffersFactory, PromocodeFactory, AssertEvents;

    public function testCalculate()
    {
        $cart = $this->generateCart();
        $cart->addOffer($this->generateOffer());
        $cart->addOffer($this->generateOffer());
        $cart->addOffer($this->generateOffer());
        $cart->usePromocode($this->generatePromocode());

        $totalPrice = array_reduce($cart->getOffers(), function ($totalPrice, $item) {
            return $totalPrice + ($item->getPrice() * $item->getQuantity());
        }, 0);

        $totalPrice -= ($totalPrice / 100) * $cart->getPromocode()->getDiscountPercent();
        $this->assertSame($totalPrice, $cart->getTotalPrice());
    }
}