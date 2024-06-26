<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferUuId;
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

    public function testReplaceOffer()
    {
        $cart = $this->generateCart();
        $offer = $this->generateOffer();
        $offerForReplace = $this->generateOffer();

        $cart->addOffer($offer);
        $cart->replaceOffer($offer, $offerForReplace);
        $this->assertSame($offerForReplace, $cart->getOffer($offerForReplace->getUuid()));

        $this->expectException(\DomainException::class);
        $cart->getOffer($offer->getUuid());
    }

    public function testReplaceOfferIfDoesNotExists()
    {
        $cart = $this->generateCart();
        $offer = $this->generateOffer();
        $offerForReplace = $this->generateOffer();

        $this->expectException(\DomainException::class);
        $cart->replaceOffer($offer, $offerForReplace);
    }

    public function testRemoveCartItemById()
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

    public function testRemoveCartItemByUuid()
    {
        $cart = $this->generateCart();
        $offer = $this->generateOffer();
        $cart->addOffer($offer);
        $cart->flushEvents();

        $cart->removeOffer($offer->getUuid());
        $this->assertEmpty($cart->getOffers());
        $this->assertEvents($cart, [new CartUpdated($cart)]);

        $this->expectException(\DomainException::class);
        $cart->getOffer($offer->getUuid());
    }

    public function testRemoveCartItemByIdIfDoesNotExists()
    {
        $cart = $this->generateCart();
        $this->expectException(\DomainException::class);
        $cart->removeOffer(OfferId::random());
    }

    public function testRemoveCartItemByUuidIfDoesNotExists()
    {
        $cart = $this->generateCart();
        $this->expectException(\DomainException::class);
        $cart->removeOffer(OfferUuid::random());
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