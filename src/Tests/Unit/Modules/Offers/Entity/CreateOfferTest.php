<?php

namespace Project\Tests\Unit\Modules\Offers\Entity;

use Project\Modules\Shopping\Offers\OfferId;
use Project\Modules\Shopping\Offers\OfferUuId;
use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;

class CreateOfferTest extends \PHPUnit\Framework\TestCase
{
    use OffersFactory;

    public function testCreateOffer()
    {
        $item = $this->makeOffer(
            $id = OfferId::random(),
            $uuid = OfferUuId::random(),
            $product = rand(1, 10),
            $name = md5(rand()),
            $regularPrice = rand(400, 500),
            $price = rand(100, 400),
            $quantity = rand(1, 10),
            $size = md5(rand()),
            $color = md5(rand()),
        );

        $this->assertTrue($id->equalsTo($item->getId()));
        $this->assertTrue($uuid->equalsTo($item->getUuid()));
        $this->assertSame($product, $item->getProduct());
        $this->assertSame($name, $item->getName());
        $this->assertSame($regularPrice, $item->getRegularPrice());
        $this->assertSame($price, $item->getPrice());
        $this->assertSame($quantity, $item->getQuantity());
        $this->assertSame($size, $item->getSize());
        $this->assertSame($color, $item->getColor());
    }

    public function testEqualsOffersWithNotSameIdAndQuantity()
    {
        $generated = $this->generateOffer();
        $created = $this->makeOffer(
            id: OfferId::next(),
            uuid: OfferUuid::random(),
            product: $generated->getProduct(),
            name: $generated->getName(),
            regularPrice: $generated->getRegularPrice(),
            price: $generated->getPrice(),
            quantity: $generated->getQuantity() + 1,
            size: $generated->getSize(),
            color: $generated->getColor(),
        );

        $this->assertFalse($generated->getId()->equalsTo($created->getId()));
        $this->assertNotSame($generated->getQuantity(), $created->getQuantity());
        $this->assertTrue($generated->equalsTo($created));
    }

    public function testEqualsOffersWithSameIds()
    {
        $generated = $this->generateOffer();
        $sameId = $this->makeOffer(
            id: $generated->getId(),
            uuid: OfferUuId::random(),
            regularPrice: 500
        );

        $sameUuid = $this->makeOffer(
            id: OfferId::random(),
            uuid: $generated->getUuid(),
            regularPrice: 600
        );

        $this->assertFalse($sameId->equalsTo($sameUuid));
        $this->assertTrue($generated->equalsTo($sameId));
        $this->assertTrue($generated->equalsTo($sameUuid));
    }

    public function testCreateOfferWithZeroQuantity()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeOffer(id: OfferId::random(), uuid: OfferUuId::random(), quantity: 0);
    }

    public function testCreateOfferWithNegativePrice()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeOffer(id: OfferId::random(), uuid: OfferUuId::random(), regularPrice: -5);
    }

	public function testCreateOfferWithNegativeRegularPrice()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->makeOffer(id: OfferId::random(), uuid: OfferUuId::random(), price: -5);
	}

	public function testCreateOfferWithPriceThatGreaterThenRegularPrice()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->makeOffer(id: OfferId::random(), uuid: OfferUuId::random(), regularPrice: 100, price: 150);
	}
}