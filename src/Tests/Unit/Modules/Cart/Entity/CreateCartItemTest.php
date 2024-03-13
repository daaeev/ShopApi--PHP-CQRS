<?php

namespace Project\Tests\Unit\Modules\Cart\Entity;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\CartFactory;
use Project\Modules\Shopping\Cart\Entity\CartItemId;

class CreateCartItemTest extends \PHPUnit\Framework\TestCase
{
    use CartFactory;

    public function testCreateCartItem()
    {
        $item = $this->makeCartItem(
            $id = CartItemId::random(),
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

    public function testEqualsCartItems()
    {
        $generated = $this->generateCartItem();
        $otherGenerated = $this->generateCartItem();
        $created = $this->makeCartItem(
            CartItemId::next(),
            $generated->getProduct(),
            $generated->getName(),
            $generated->getRegularPrice(),
            $generated->getPrice(),
            $generated->getQuantity() + 1,
            $generated->getSize(),
            $generated->getColor(),
        );

        $this->assertFalse($generated->getId()->equalsTo($created->getId()));
        $this->assertNotSame($generated->getQuantity(), $created->getQuantity());
        $this->assertTrue($generated->equalsTo($created));
        $this->assertFalse($generated->equalsTo($otherGenerated));
    }

    public function testCreateCartItemWithZeroQuantity()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeCartItem(
            CartItemId::random(),
            rand(1, 10),
            md5(rand()),
            (float) rand(400, 500),
            (float) rand(100, 400),
            0,
            md5(rand()),
            md5(rand()),
        );
    }

    public function testCreateCartItemWithNegativePrice()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeCartItem(
            CartItemId::random(),
            rand(1, 10),
            md5(rand()),
            -5,
            rand(10, 500),
            rand(1, 10),
            md5(rand()),
            md5(rand()),
        );
    }

	public function testCreateCartItemWithNegativeRegularPrice()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->makeCartItem(
			CartItemId::random(),
			rand(1, 10),
			md5(rand()),
			rand(10, 500),
			-5,
			rand(1, 10),
			md5(rand()),
			md5(rand()),
		);
	}

	public function testCreateCartItemWithPriceThatGreaterThenRegularPrice()
	{
		$this->expectException(InvalidArgumentException::class);
		$this->makeCartItem(
			CartItemId::random(),
			rand(1, 10),
			md5(rand()),
			100,
			150,
			rand(1, 10),
			md5(rand()),
			md5(rand()),
		);
	}
}