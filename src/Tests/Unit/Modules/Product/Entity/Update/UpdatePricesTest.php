<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use DomainException;
use Project\Common\Currency;
use Webmozart\Assert\InvalidArgumentException;
use Project\Modules\Product\Entity\Price\Price;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Api\Events\ProductUpdated;
use Project\Tests\Unit\Modules\Helpers\AssertEventsTrait;
use Project\Modules\Product\Api\Events\ProductPricesChanged;

class UpdatePricesTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEventsTrait;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $updatedPrices = [];

        foreach ($product->getPrices() as $price) {
            $updatedPrices[] = new Price(
                $price->getCurrency(),
                $price->getPrice() + rand(100, 500)
            );
        }

        $this->assertFalse($product->samePrices($updatedPrices));
        $product->setPrices($updatedPrices);
        $this->assertTrue($product->samePrices($updatedPrices));

        foreach ($updatedPrices as $updatedPrice) {
            $this->assertArrayHasKey($updatedPrice->getCurrency()->value, $product->getPrices());
            $this->assertTrue($updatedPrice->equalsTo($product->getPrices()[$updatedPrice->getCurrency()->value]));
        }

        $this->assertEvents($product, [
            new ProductPricesChanged($product),
            new ProductUpdated($product),
        ]);
    }

    public function testUpdateWithInvalidPricesData()
    {
        $product = $this->generateProduct();
        $this->expectException(InvalidArgumentException::class);
        $product->setPrices([
            'Invalid price data'
        ]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $prices = $product->getPrices();
        $product->setPrices($prices);
        $product->samePrices($prices);
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateWithRepeatingPrices()
    {
        $product = $this->generateProduct();
        $prices = $this->makePrices();
        $initialCount = count($prices);
        $clonedPrice = array_shift($prices);
        $prices[] = clone $clonedPrice;
        $prices[] = clone $clonedPrice;
        $prices[] = clone $clonedPrice;
        $this->assertFalse($product->samePrices($prices));
        $product->setPrices($prices);
        $this->assertCount($initialCount, $product->getPrices());
        $this->assertNotCount(count($prices), $product->getPrices());

        foreach ($prices as $price) {
            $this->assertArrayHasKey($price->getCurrency()->value, $product->getPrices());
        }
    }

    public function testUpdateWithoutAnyActiveCurrencyPrice()
    {
        $product = $this->generateProduct();
        $prices = $this->makePrices();
        array_shift($prices);
        $this->expectException(DomainException::class);
        $product->setPrices($prices);
    }

    public function testEqualsPrices()
    {
        $price = new Price(
            Currency::default(),
            rand(100, 500)
        );
        $this->assertTrue($price->equalsTo($price));
        $otherPrice = new Price(
            Currency::default(),
            $price->getPrice() + rand(10, 50)
        );
        $this->assertFalse($price->equalsTo($otherPrice));
    }
}