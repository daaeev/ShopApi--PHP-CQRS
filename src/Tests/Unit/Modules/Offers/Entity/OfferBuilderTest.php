<?php

namespace Project\Tests\Unit\Modules\Offers\Entity;

use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;

class OfferBuilderTest extends \PHPUnit\Framework\TestCase
{
    use OffersFactory;

    public function testBuildFromAnotherOffer()
    {
        $builder = new OfferBuilder();
        $offer = $this->generateOffer();
        $builded = $builder->from($offer)->build();

        $this->assertNotSame($builded->getId(), $offer->getId());
        $this->assertNotSame($builded->getUuid(), $offer->getUuid());
        $this->assertSame($builded->getId()->getId(), $offer->getId()->getId());
        $this->assertSame($builded->getProduct(), $offer->getProduct());
        $this->assertSame($builded->getName(), $offer->getName());
        $this->assertSame($builded->getRegularPrice(), $offer->getRegularPrice());
        $this->assertSame($builded->getPrice(), $offer->getPrice());
        $this->assertSame($builded->getQuantity(), $offer->getQuantity());
        $this->assertSame($builded->getSize(), $offer->getSize());
        $this->assertSame($builded->getColor(), $offer->getColor());
    }

    public function testWithNullableId()
    {
        $builder = new OfferBuilder();
        $offer = $this->generateOffer();
        $builded = $builder->from($offer)->withNullableId()->build();
        $this->assertNull($builded->getId()->getId());
    }

    public function testWithPrice()
    {
        $builder = new OfferBuilder();
        $offer = $this->generateOffer();
        $builded = $builder->from($offer)
            ->withPrice($offer->getPrice() + 50)
            ->build();

        $this->assertSame($builded->getPrice(), $offer->getPrice() + 50);
    }

    public function testWithQuantity()
    {
        $builder = new OfferBuilder;
        $offer = $this->generateOffer();
        $builded = $builder->from($offer)
            ->withQuantity($offer->getQuantity() + 1)
            ->build();

        $this->assertSame($builded->getQuantity(), $offer->getQuantity() + 1);
    }
}