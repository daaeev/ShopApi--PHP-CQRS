<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use DomainException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;
use Project\Modules\Catalogue\Api\Events\Product\ProductActivityChanged;

class UpdateActivityTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testActivate()
    {
        $product = $this->generateProduct();
        $product->deactivate();
        $this->assertFalse($product->isActive());
        $product->flushEvents();
        $product->activate();
        $this->assertTrue($product->isActive());
        $this->assertEvents($product, [
            new ProductActivityChanged($product),
            new ProductUpdated($product),
        ]);
    }

    public function testActivateIfAlreadyActive()
    {
        $product = $this->generateProduct();
        $this->assertTrue($product->isActive());
        $product->activate();
        $this->assertTrue($product->isActive());
        $this->assertEmpty($product->flushEvents());
    }

    public function testActivateWithoutActiveCurrenciesPrices()
    {
        $product = $this->generateProduct();
        $product->deactivate();
        $product->setPrices([]);
        $product->flushEvents();
        $this->assertFalse($product->isActive());
        $this->assertEmpty($product->getPrices());
        $this->expectException(DomainException::class);
        $product->activate();
    }

    public function testDeactivate()
    {
        $product = $this->generateProduct();
        $this->assertTrue($product->isActive());
        $product->deactivate();
        $this->assertFalse($product->isActive());
        $this->assertEvents($product, [
            new ProductActivityChanged($product),
            new ProductUpdated($product),
        ]);
    }

    public function testDeactivateIfAlreadyDeactivated()
    {
        $product = $this->generateProduct();
        $this->assertTrue($product->isActive());
        $product->deactivate();
        $product->flushEvents();
        $product->deactivate();
        $this->assertFalse($product->isActive());
        $this->assertEmpty($product->flushEvents());
    }
}