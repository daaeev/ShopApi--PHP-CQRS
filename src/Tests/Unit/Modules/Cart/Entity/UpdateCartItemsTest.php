<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Modules\Shopping\Entity\OfferId;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Modules\Shopping\Api\Events\Cart\CartUpdated;

class UpdateCartItemsTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory, OffersFactory, AssertEvents;

    public function testAddItem()
    {
        $cart = $this->generateCart();
        $offer = $this->generateOffer();
        $cart->addOffer($offer);
        $this->assertNotEmpty($cart->getUpdatedAt());
        $this->assertCount(1, $cart->getOffers());

        $foundItem = $cart->getOffer($offer->getId());
        $this->assertSame($offer, $foundItem);
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }

    public function testRemoveCartItem()
    {
        $cart = $this->generateCart();
        $offer = $this->generateOffer();
        $cart->addOffer($offer);
        $cart->flushEvents();

        $cart->removeOffer($offer->getId());
        $this->assertEmpty($cart->getOffers());
        $this->assertEvents($cart, [new CartUpdated($cart)]);

        $this->expectException(\DomainException::class);
        $cart->getOffer($offer->getId());
    }

    public function testRemoveCartItemIfDoesNotExists()
    {
        $cart = $this->generateCart();
        $this->expectException(\DomainException::class);
        $cart->removeOffer(OfferId::random());
    }

    public function testSetCartItems()
    {
        $cart = $this->generateCart();
        $cart->addOffer($this->generateOffer());
        $cart->flushEvents();

        $newOffers = [$this->generateOffer(), $this->generateOffer()];
        $oldUpdatedAt = $cart->getUpdatedAt();

        $cart->setOffers($newOffers);
        $this->assertNotSame($oldUpdatedAt, $cart->getUpdatedAt());
        $this->assertSame($cart->getOffers(), $newOffers);
        $this->assertEvents($cart, [new CartUpdated($cart)]);
    }
}