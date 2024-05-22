<?php

namespace Project\Tests\Unit\Modules\Offers\Entity;

use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferUuId;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Modules\Shopping\Offers\OffersCollection;

class OffersCollectionTest extends \PHPUnit\Framework\TestCase
{
    use OffersFactory;

    private readonly OffersCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new OffersCollection;
    }

    public function testAddOffer()
    {
        $offer = $this->generateOffer();
        $this->collection->add($offer);
        $this->assertSame($offer, $this->collection->get($offer->getId()));
        $this->assertSame($offer, $this->collection->get($offer->getUuid()));
    }

    public function testAddEqualsOfferWithAnotherId()
    {
        $offer = $this->makeOffer(id: OfferId::random(), uuid: OfferUuId::random());
        $this->collection->add($offer);

        $anotherEqualsOffer = $this->makeOffer(
            id: OfferId::random(),
            uuid: OfferUuId::random(),
            quantity: $offer->getQuantity() + 1,
        );

        $this->collection->add($anotherEqualsOffer);
        $this->assertCount(1, $this->collection->all());

        $foundOffer = $this->collection->get($anotherEqualsOffer->getId());
        $this->assertNotSame($offer, $foundOffer);
        $this->assertSame($anotherEqualsOffer, $foundOffer);

        $this->expectException(\DomainException::class);
        $this->collection->get($offer->getId());
    }

    public function testAddAnotherOfferWithSameId()
    {
        $offer = $this->generateOffer();
        $anotherOffer = $this->makeOffer(
            id: $offer->getId(),
            uuid: OfferUuId::random(),
            regularPrice: $offer->getRegularPrice() + 5
        );

        $this->collection->add($anotherOffer);
        $this->assertCount(1, $this->collection->all());

        $foundOffer = $this->collection->get($anotherOffer->getId());
        $this->assertNotSame($offer, $foundOffer);
        $this->assertSame($anotherOffer, $foundOffer);
    }

    public function testAddAnotherOfferWithSameUuid()
    {
        $offer = $this->generateOffer();
        $anotherOffer = $this->makeOffer(
            id: OfferId::next(),
            uuid: $offer->getUuid(),
            regularPrice: $offer->getRegularPrice() + 5
        );

        $this->collection->add($anotherOffer);
        $this->assertCount(1, $this->collection->all());

        $foundOffer = $this->collection->get($anotherOffer->getUuid());
        $this->assertNotSame($offer, $foundOffer);
        $this->assertSame($anotherOffer, $foundOffer);
    }

    public function testReplaceById()
    {
        $offer = $this->generateOffer();
        $this->collection->add($offer);

        $this->collection->replace($offer->getId(), $newOffer = $this->generateOffer());
        $this->assertSame($newOffer, $this->collection->get($newOffer->getId()));

        $this->expectException(\DomainException::class);
        $this->collection->get($offer->getId());
    }

    public function testReplaceByUuid()
    {
        $offer = $this->generateOffer();
        $this->collection->add($offer);

        $this->collection->replace($offer->getUuid(), $newOffer = $this->generateOffer());
        $this->assertSame($newOffer, $this->collection->get($newOffer->getUuid()));

        $this->expectException(\DomainException::class);
        $this->collection->get($offer->getUuid());
    }

    public function testReplaceByIdIfReplacedOfferDoesNotExists()
    {
        $this->expectException(\DomainException::class);
        $this->collection->replace(OfferId::random(), $this->generateOffer());
    }

    public function testReplaceByUuidIfReplacedOfferDoesNotExists()
    {
        $this->expectException(\DomainException::class);
        $this->collection->replace(OfferUuid::random(), $this->generateOffer());
    }

    public function testSetOffers()
    {
        $this->collection->add($this->generateOffer());
        $newOffers = [$this->generateOffer(), $this->generateOffer()];
        $this->collection->set($newOffers);
        $this->assertSame($this->collection->all(), $newOffers);
    }

    public function testRemoveById()
    {
        $offer = $this->generateOffer();
        $this->collection->add($offer);

        $this->collection->remove($offer->getId());
        $this->assertEmpty($this->collection->all());

        $this->expectException(\DomainException::class);
        $this->collection->get($offer->getId());
    }

    public function testRemoveByUuid()
    {
        $offer = $this->generateOffer();
        $this->collection->add($offer);

        $this->collection->remove($offer->getUuid());
        $this->assertEmpty($this->collection->all());

        $this->expectException(\DomainException::class);
        $this->collection->get($offer->getUuid());
    }

    public function testRemoveOfferIfDoesNotExists()
    {
        $this->expectException(\DomainException::class);
        $this->collection->remove(OfferUuId::random());
    }
}