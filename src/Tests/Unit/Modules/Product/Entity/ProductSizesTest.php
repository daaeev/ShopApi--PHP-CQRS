<?php

namespace Project\Tests\Unit\Modules\Product\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;

class ProductSizesTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $sizes = [uniqid(), uniqid()];
        $product->setSizes($sizes);

        $this->assertCount(2, $product->getSizes());
        $this->assertSame($sizes, $product->getSizes());
        $this->assertNotEmpty($product->getUpdatedAt());
        $this->assertEvents($product, [new ProductUpdated($product)]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $sizes = [uniqid(), uniqid()];
        $product->setSizes($sizes);
        $oldUpdatedAt = $product->getUpdatedAt();
        $product->flushEvents();

        $product->setSizes($sizes);

        $this->assertCount(2, $product->getSizes());
        $this->assertSame($sizes, $product->getSizes());
        $this->assertSame($oldUpdatedAt, $product->getUpdatedAt());
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateWithRepeatingSizes()
    {
        $product = $this->generateProduct();
        $clonedSize = uniqid();
        $sizes = [$clonedSize, $clonedSize, uniqid()];
        $product->setSizes($sizes);

        $this->assertCount(2, $product->getSizes());
        foreach ($sizes as $size) {
            $this->assertTrue(in_array($size, $product->getSizes()));
        }
    }
}
