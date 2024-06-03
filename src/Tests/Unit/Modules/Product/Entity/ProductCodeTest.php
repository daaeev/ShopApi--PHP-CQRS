<?php

namespace Project\Tests\Unit\Modules\Product\Entity;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;
use Project\Modules\Catalogue\Api\Events\Product\ProductCodeChanged;

class ProductCodeTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $initialCode = $product->getCode();
        $updatedCode = uniqid();
        $product->setCode($updatedCode);
        $this->assertEquals($updatedCode, $product->getCode());
        $this->assertNotEquals($initialCode, $product->getCode());
        $this->assertNotEmpty($product->getUpdatedAt());
        $this->assertEvents($product, [new ProductCodeChanged($product), new ProductUpdated($product)]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $product->setCode($product->getCode());
        $this->assertNull($product->getUpdatedAt());
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $product = $this->generateProduct();
        $product->setCode('');
    }
}
