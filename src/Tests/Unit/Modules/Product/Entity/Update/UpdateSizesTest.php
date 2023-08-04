<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;

class UpdateSizesTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $this->assertEmpty($product->getSizes());
        $sizes = [
            md5(rand()),
            md5(rand()),
        ];
        $this->assertFalse($product->sameSizes($sizes));
        $product->setSizes($sizes);

        $this->assertTrue($product->sameSizes($sizes));
        $this->assertCount(2, $product->getSizes());
        $this->assertSame($sizes, $product->getSizes());
        $this->assertEvents($product, [new ProductUpdated($product)]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $sizes = [
            md5(rand()),
            md5(rand()),
        ];
        $product->setSizes($sizes);
        $product->flushEvents();
        $product->setSizes($sizes);

        $this->assertCount(2, $product->getSizes());
        $this->assertSame($sizes, $product->getSizes());
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateWithRepeatingSizes()
    {
        $product = $this->generateProduct();
        $clonedSize = md5(rand());
        $sizes = [
            $clonedSize,
            $clonedSize,
            md5(rand()),
        ];
        $product->setSizes($sizes);

        $this->assertFalse($product->sameSizes($sizes));
        $this->assertCount(2, $product->getSizes());
        foreach ($sizes as $size) {
            $this->assertTrue(in_array($size, $product->getSizes()));
        }
    }
}