<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Modules\Shopping\Entity\Promocode;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Tests\Unit\Modules\Helpers\PromocodeFactory;

class CalculateCartPriceTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, OffersFactory, PromocodeFactory, AssertEvents;

    public function testCalculateAfterAddOffer()
    {
        $cart = $this->generateCart();
        $this->assertSame(0, $cart->getTotalPrice());
        $this->assertSame(0, $cart->getRegularPrice());

        $cart->addOffer($this->generateOffer());
        $this->assertNotSame(0, $cart->getTotalPrice());
        $this->assertNotSame(0, $cart->getRegularPrice());

        $this->assertSame($this->calculateTotalPrice($cart), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart), $cart->getRegularPrice());
    }

    private function calculateTotalPrice(Cart $cart): int
    {
        $totalPrice = array_reduce($cart->getOffers(), function ($totalPrice, $item) {
            return $totalPrice + ($item->getPrice() * $item->getQuantity());
        }, 0);

        if (null !== $cart->getPromocode()) {
            $totalPrice -= ($totalPrice / 100) * $cart->getPromocode()->getDiscountPercent();
        }

        return $totalPrice;
    }

    private function calculateRegularPrice(Cart $cart): int
    {
        return array_reduce($cart->getOffers(), function ($totalPrice, $item) {
            return $totalPrice + ($item->getRegularPrice() * $item->getQuantity());
        }, 0);
    }

    public function testCalculateAfterSetOffers()
    {
        $cart = $this->generateCart();
        $this->assertSame(0, $cart->getTotalPrice());
        $this->assertSame(0, $cart->getRegularPrice());

        $cart->setOffers([$this->generateOffer(), $this->generateOffer()]);
        $this->assertNotSame(0, $cart->getTotalPrice());
        $this->assertNotSame(0, $cart->getRegularPrice());

        $this->assertSame($this->calculateTotalPrice($cart), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart), $cart->getRegularPrice());
    }

    public function testCalculateAfterUsePromocode()
    {
        $cart = $this->generateCart();
        $cart->setOffers([$this->generateOffer(), $this->generateOffer()]);
        $oldTotalPrice = $cart->getTotalPrice();
        $oldRegularPrice = $cart->getRegularPrice();

        $cart->usePromocode(Promocode::fromBaseEntity($this->generatePromocode()));
        $this->assertNotSame($oldTotalPrice, $cart->getTotalPrice());
        $this->assertSame($oldRegularPrice, $cart->getRegularPrice());

        $this->assertSame($this->calculateTotalPrice($cart), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart), $cart->getRegularPrice());
    }

    public function testCalculateAfterRemovePromocode()
    {
        $cart = $this->generateCart();
        $cart->setOffers([$this->generateOffer(), $this->generateOffer()]);
        $cart->usePromocode(Promocode::fromBaseEntity($this->generatePromocode()));
        $oldTotalPrice = $cart->getTotalPrice();
        $oldRegularPrice = $cart->getRegularPrice();

        $cart->removePromocode();
        $this->assertNotSame($oldTotalPrice, $cart->getTotalPrice());
        $this->assertSame($oldRegularPrice, $cart->getRegularPrice());

        $this->assertSame($this->calculateTotalPrice($cart), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart), $cart->getRegularPrice());
    }
}