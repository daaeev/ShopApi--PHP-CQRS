<?php

namespace Project\Tests\Unit\Modules\Product\Entity;

use DomainException;
use Project\Common\Product\Currency;
use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Product\Entity\Price\Price;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;
use Project\Modules\Catalogue\Api\Events\Product\ProductPricesChanged;

class ProductPricesTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

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

        $product->setPrices($updatedPrices);

        foreach ($updatedPrices as $updatedPrice) {
            $this->assertArrayHasKey($updatedPrice->getCurrency()->value, $product->getPrices());
            $this->assertTrue($updatedPrice->equalsTo($product->getPrices()[$updatedPrice->getCurrency()->value]));
        }

        $this->assertNotEmpty($product->getUpdatedAt());
        $this->assertEvents($product, [new ProductPricesChanged($product), new ProductUpdated($product)]);
    }

    public function testUpdateWithInvalidPrice()
    {
        $product = $this->generateProduct();
        $this->expectException(InvalidArgumentException::class);
        $product->setPrices(['Invalid price']);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $prices = $product->getPrices();
        $product->setPrices($prices);

        foreach ($prices as $price) {
            $this->assertArrayHasKey($price->getCurrency()->value, $product->getPrices());
            $this->assertTrue($price->equalsTo($product->getPrices()[$price->getCurrency()->value]));
        }

        $this->assertNull($product->getUpdatedAt());
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

        $product->setPrices($prices);

        $this->assertCount($initialCount, $product->getPrices());
        $this->assertNotCount(count($prices), $product->getPrices());

        foreach ($prices as $price) {
            $this->assertArrayHasKey($price->getCurrency()->value, $product->getPrices());
        }
    }

    public function testUpdateWithoutAllActiveCurrenciesPrices()
    {
        $product = $this->generateProduct();
        $prices = $this->makePrices();
        array_shift($prices);
        $this->expectException(DomainException::class);
        $product->setPrices($prices);
    }

    public function testEqualsPrices()
    {
        $price = new Price(Currency::default(), rand(100, 500));
        $this->assertTrue($price->equalsTo($price));
        $otherPrice = new Price(Currency::default(), $price->getPrice() + rand(10, 50));
        $this->assertFalse($price->equalsTo($otherPrice));
    }
}
