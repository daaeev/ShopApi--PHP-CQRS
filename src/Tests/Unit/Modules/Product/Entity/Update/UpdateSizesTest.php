<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Project\Modules\Product\Entity\Product;
use Project\Modules\Product\Entity\Size\Size;
use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Api\Events\ProductUpdated;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;

class UpdateSizesTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $this->assertEmpty($product->getSizes());
        $sizes = [
            new Size(md5(rand())),
            new Size(md5(rand())),
        ];
        $this->assertFalse($product->sameSizes($sizes));
        $product->setSizes($sizes);

        $this->assertTrue($product->sameSizes($sizes));
        $this->assertCount(2, $product->getSizes());
        $this->assertSameSizes($sizes, $product);
        $this->assertEvents($product, [new ProductUpdated($product)]);
    }

    private function assertSameSizes(array $sizes, Product $product): void
    {
        foreach ($sizes as $size) {
            $this->assertArrayHasKey($size->getSize(), $product->getSizes());
            $this->assertEquals($size->getSize(), $product->getSizes()[$size->getSize()]->getSize());
        }
    }


    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $sizes = [
            new Size(md5(rand())),
            new Size(md5(rand())),
        ];
        $product->setSizes($sizes);
        $product->flushEvents();
        $product->setSizes($sizes);

        $this->assertCount(2, $product->getSizes());
        $this->assertSameSizes($sizes, $product);
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateWithRepeatingSizes()
    {
        $product = $this->generateProduct();
        $size = md5(rand());
        $sizes = [
            new Size($size),
            new Size($size),
            new Size($size),
            new Size(md5(rand())),
        ];
        $product->setSizes($sizes);

        $this->assertFalse($product->sameSizes($sizes));
        $this->assertCount(2, $product->getSizes());
        $this->assertSameSizes($sizes, $product);
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