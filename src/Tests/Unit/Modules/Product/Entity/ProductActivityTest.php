<?php

namespace Project\Tests\Unit\Modules\Product\Entity;

use DomainException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;
use Project\Modules\Catalogue\Api\Events\Product\ProductActivityChanged;

class ProductActivityTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testActivate()
    {
        $product = $this->generateProduct();
        $product->deactivate();
        $oldUpdatedAt = $product->getUpdatedAt();
        $product->flushEvents();

        $product->activate();

        $this->assertTrue($product->isActive());
        $this->assertNotSame($oldUpdatedAt, $product->getUpdatedAt());
        $this->assertEvents($product, [new ProductActivityChanged($product), new ProductUpdated($product)]);
    }

    public function testActivateIfAlreadyActive()
    {
        $product = $this->generateProduct();
        $product->activate();
        $this->assertTrue($product->isActive());
        $this->assertNull($product->getUpdatedAt());
        $this->assertEmpty($product->flushEvents());
    }

    public function testActivateWithoutActiveCurrenciesPrices()
    {
        $product = $this->generateProduct();
        $product->deactivate();
        $product->setPrices([]);

        $this->expectException(DomainException::class);
        $product->activate();
    }

    public function testDeactivate()
    {
        $product = $this->generateProduct();
        $product->deactivate();
        $this->assertFalse($product->isActive());
        $this->assertNotEmpty($product->getUpdatedAt());
        $this->assertEvents($product, [new ProductActivityChanged($product), new ProductUpdated($product)]);
    }

    public function testDeactivateIfAlreadyDeactivated()
    {
        $product = $this->generateProduct();
        $product->deactivate();
        $oldUpdatedAt = $product->getUpdatedAt();
        $product->flushEvents();

        $product->deactivate();

        $this->assertFalse($product->isActive());
        $this->assertSame($oldUpdatedAt, $product->getUpdatedAt());
        $this->assertEmpty($product->flushEvents());
    }
}
