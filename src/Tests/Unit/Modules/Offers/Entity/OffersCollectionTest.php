<?php

namespace Project\Tests\Unit\Modules\Offers\Entity;

use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferBuilder;
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
    }

    public function testAddEqualsOfferWithAnotherId()
    {
        $offer = $this->generateOffer();
        $this->collection->add($offer);

        $anotherEqualsOffer = $this->makeOffer(
            id: OfferId::random(),
            product: $offer->getProduct(),
            name: 'Other name',
            regularPrice: $offer->getRegularPrice(),
            price: $offer->getPrice(),
            quantity: $offer->getQuantity() + 1,
            size: $offer->getSize(),
            color: $offer->getColor(),
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
        $builder = new OfferBuilder;
        $anotherOffer = $builder->from($offer)
            ->withPrice($offer->getPrice() + 10)
            ->build();

        $this->collection->add($anotherOffer);
        $this->assertCount(1, $this->collection->all());

        $foundOffer = $this->collection->get($anotherOffer->getId());
        $this->assertNotSame($offer, $foundOffer);
        $this->assertSame($anotherOffer, $foundOffer);
    }

    public function testSetOffer()
    {
        $this->collection->add($this->generateOffer());
        $newOffers = [$this->generateOffer(), $this->generateOffer()];
        $this->collection->set($newOffers);
        $this->assertSame($this->collection->all(), $newOffers);
    }

    public function testRemoveOffer()
    {
        $offer = $this->generateOffer();
        $this->collection->add($offer);

        $this->collection->remove($offer->getId());
        $this->assertEmpty($this->collection->all());

        $this->expectException(\DomainException::class);
        $this->collection->get($offer->getId());
    }

    public function testRemoveOfferIfDoesNotExists()
    {
        $this->expectException(\DomainException::class);
        $this->collection->remove(OfferId::random());
    }
}