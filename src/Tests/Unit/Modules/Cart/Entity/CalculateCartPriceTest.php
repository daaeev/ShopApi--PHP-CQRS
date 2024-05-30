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
        $cart->addOffer($this->generateOffer());
        $this->assertNotSame(0, $cart->getTotalPrice());
        $this->assertNotSame(0, $cart->getRegularPrice());

        $this->assertSame($this->calculateTotalPrice($cart->getOffers(), $cart->getPromocode()), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart->getOffers()), $cart->getRegularPrice());
    }
    public function testCalculateAfterReplaceOffer()
    {
        $cart = $this->generateCart();
        $offer = $this->generateOffer();
        $cart->addOffer($offer);
        $oldTotalPrice = $cart->getTotalPrice();
        $oldRegularPrice = $cart->getRegularPrice();

        $cart->replaceOffer($offer, $this->generateOffer());
        $this->assertNotSame($oldTotalPrice, $cart->getTotalPrice());
        $this->assertNotSame($oldRegularPrice, $cart->getRegularPrice());

        $this->assertSame($this->calculateTotalPrice($cart->getOffers(), $cart->getPromocode()), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart->getOffers()), $cart->getRegularPrice());
    }

    public function testCalculateAfterRemoveOffer()
    {
        $cart = $this->generateCart();
        $cart->setOffers([$offer = $this->generateOffer(), $this->generateOffer()]);
        $oldTotalPrice = $cart->getTotalPrice();
        $oldRegularPrice = $cart->getRegularPrice();

        $cart->removeOffer($offer->getId());
        $this->assertNotSame($oldTotalPrice, $cart->getTotalPrice());
        $this->assertNotSame($oldRegularPrice, $cart->getRegularPrice());

        $this->assertSame($this->calculateTotalPrice($cart->getOffers(), $cart->getPromocode()), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart->getOffers()), $cart->getRegularPrice());
    }

    public function testCalculateAfterSetOffers()
    {
        $cart = $this->generateCart();
        $cart->setOffers([$this->generateOffer(), $this->generateOffer()]);
        $this->assertNotSame(0, $cart->getTotalPrice());
        $this->assertNotSame(0, $cart->getRegularPrice());

        $this->assertSame($this->calculateTotalPrice($cart->getOffers(), $cart->getPromocode()), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart->getOffers()), $cart->getRegularPrice());
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

        $this->assertSame($this->calculateTotalPrice($cart->getOffers(), $cart->getPromocode()), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart->getOffers()), $cart->getRegularPrice());
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

        $this->assertSame($this->calculateTotalPrice($cart->getOffers(), $cart->getPromocode()), $cart->getTotalPrice());
        $this->assertSame($this->calculateRegularPrice($cart->getOffers()), $cart->getRegularPrice());
    }
}