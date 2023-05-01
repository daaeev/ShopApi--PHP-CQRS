<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Project\Modules\Product\Entity\Size\Size;
use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Api\Events\ProductUpdated;
use Project\Tests\Unit\Modules\Helpers\AssertEventsTrait;

class UpdateSizesTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEventsTrait;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $this->assertEmpty($product->getSizes());
        $sizes = [
            Size::S,
            Size::L,
        ];
        $this->assertFalse($product->sameSizes($sizes));
        $product->setSizes($sizes);

        $this->assertTrue($product->sameSizes($sizes));
        $this->assertCount(2, $product->getSizes());
        foreach ($sizes as $size) {
            $this->assertArrayHasKey($size->value, $product->getSizes());
            $this->assertEquals($size, $product->getSizes()[$size->value]);
        }
        $this->assertEvents($product, [new ProductUpdated($product)]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $sizes = [
            Size::S,
            Size::L,
        ];
        $product->setSizes($sizes);
        $product->flushEvents();
        $product->setSizes($sizes);

        $this->assertCount(2, $product->getSizes());
        foreach ($sizes as $size) {
            $this->assertArrayHasKey($size->value, $product->getSizes());
            $this->assertEquals($size, $product->getSizes()[$size->value]);
        }
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateWithRepeatingSizes()
    {
        $product = $this->generateProduct();
        $sizes = [
            Size::S,
            Size::S,
            Size::S,
            Size::L,
        ];
        $product->setSizes($sizes);

        $this->assertFalse($product->sameSizes($sizes));
        $this->assertCount(2, $product->getSizes());
        foreach ($sizes as $size) {
            $this->assertArrayHasKey($size->value, $product->getSizes());
            $this->assertEquals($size, $product->getSizes()[$size->value]);
        }
    }

    public function testUpdateWithInvalidSizesData()
    {
        $this->expectException(InvalidArgumentException::class);
        $product = $this->generateProduct();
        $product->setSizes([
            'Invalid size data'
        ]);
    }
}