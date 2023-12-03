<?php

namespace Project\Tests\Unit\Modules\Product\Entity;

use DomainException;
use Project\Common\Product\Availability;
use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Product\Entity\ProductId;
use Project\Modules\Catalogue\Api\Events\Product\ProductCreated;

class CreateProductTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testCreate()
    {
        $product = $this->makeProduct(
            $id = ProductId::random(),
            $name = 'Test product',
            $code = 'test-product',
            $prices = $this->makePrices()
        );

        $this->assertTrue($product->getId()->equalsTo($id));
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($code, $product->getCode());
        $this->assertTrue($product->isActive());
        $this->assertEquals(Availability::IN_STOCK, $product->getAvailability());
        $this->assertEmpty($product->getColors());
        $this->assertEmpty($product->getSizes());
        $this->assertNotEmpty($product->getCreatedAt());
        $this->assertNull($product->getUpdatedAt());
        $this->assertEvents($product, [new ProductCreated($product)]);

        $this->assertTrue($product->samePrices($prices));
        foreach ($prices as $price) {
            $this->assertArrayHasKey($price->getCurrency()->value, $product->getPrices());
            $this->assertTrue($price->equalsTo($product->getPrices()[$price->getCurrency()->value]));
        }
    }

    public function testCreateWithRepeatingPricesCurrencies()
    {
        $prices = $this->makePrices();
        $initialCount = count($prices);
        $clonedPrice = array_shift($prices);
        $prices[] = clone $clonedPrice;
        $prices[] = clone $clonedPrice;
        $prices[] = clone $clonedPrice;
        $product = $this->makeProduct(
            ProductId::random(),
            'Test product',
            'test-product',
            $prices
        );

        $this->assertCount($initialCount, $product->getPrices());
        $this->assertNotCount(count($prices), $product->getPrices());
    }

    public function testCreateWithEmptyName()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeProduct(
            ProductId::next(),
            '',
            'test-product'
        );
    }

    public function testCreateWithEmptyCode()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeProduct(
            ProductId::next(),
            'Test product',
            '',
        );
    }

    public function testCreateWithInvalidPricesData()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->makeProduct(
            ProductId::next(),
            'Test product',
            '',
            [
                'Invalid data'
            ]
        );
    }

    public function testCreateWithoutPrices()
    {
        $this->expectException(DomainException::class);
        $this->makeProduct(
            ProductId::next(),
            'Test product',
            'test-product',
        );
    }

    public function testCreateWithoutAnyActiveCurrencyPrice()
    {
        $this->expectException(DomainException::class);
        $prices = $this->makePrices();
        array_shift($prices);
        $this->makeProduct(
            ProductId::next(),
            'Test product',
            'test-product',
            $prices
        );
    }
}