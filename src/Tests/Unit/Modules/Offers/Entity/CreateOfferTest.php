<?php

namespace Project\Tests\Unit\Modules\Offers\Entity;

use Project\Modules\Shopping\Offers\OfferId;
use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;

class CreateOfferTest extends \PHPUnit\Framework\TestCase
{
    use OffersFactory;

    public function testCreateOffer()
    {
        $item = $this->makeOffer(
            $id = OfferId::random(),
            $product = rand(1, 10),
            $name = md5(rand()),
            $regularPrice = (float) rand(400, 500),
            $price = (float) rand(100, 400),
            $quantity = rand(1, 10),
            $size = md5(rand()),
            $color = md5(rand()),
        );

        $this->assertTrue($id->equalsTo($item->getId()));
        $this->assertSame($product, $item->getProduct());
        $this->assertSame($name, $item->getName());
        $this->assertSame($regularPrice, $item->getRegularPrice());
        $this->assertSame($price, $item->getPrice());
        $this->assertSame($quantity, $item->getQuantity());
        $this->assertSame($size, $item->getSize());
        $this->assertSame($color, $item->getColor());
    }

    public function testEqualsOffers()
    {
        $generated = $this->generateOffer();
        $created = $this->makeOffer(
            id: OfferId::next(),
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

    public function testCreateOfferWithZeroQuantity()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeOffer(
            id: OfferId::random(),
            product: rand(1, 10),
            name: md5(rand()),
            regularPrice: (float) rand(400, 500),
            price: (float) rand(100, 400),
            quantity: 0,
            size: md5(rand()),
            color: md5(rand()),
        );
    }

    public function testCreateOfferWithNegativePrice()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeOffer(
            id: OfferId::random(),
            product: rand(1, 10),
            name: md5(rand()),
            regularPrice: -5,
            price: rand(10, 500),
            quantity: rand(1, 10),
            size: md5(rand()),
            color: md5(rand()),
        );
    }

	public function testCreateOfferWithNegativeRegularPrice()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->makeOffer(
			id: OfferId::random(),
			product: rand(1, 10),
			name: md5(rand()),
			regularPrice: rand(10, 500),
			price: -5,
			quantity: rand(1, 10),
			size: md5(rand()),
			color: md5(rand()),
		);
	}

	public function testCreateOfferWithPriceThatGreaterThenRegularPrice()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->makeOffer(
			id: OfferId::random(),
			product: rand(1, 10),
			name: md5(rand()),
			regularPrice: 100,
			price: 150,
			quantity: rand(1, 10),
			size: md5(rand()),
			color: md5(rand()),
		);
	}
}