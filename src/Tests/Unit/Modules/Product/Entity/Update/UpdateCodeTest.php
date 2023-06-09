<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Api\Events\ProductUpdated;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Modules\Product\Api\Events\ProductCodeChanged;

class UpdateCodeTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $initialCode = $product->getCode();
        $updatedCode = md5(rand());
        $product->setCode($updatedCode);
        $this->assertEquals($updatedCode, $product->getCode());
        $this->assertNotEquals($initialCode, $product->getCode());
        $this->assertEvents($product, [
            new ProductCodeChanged($product),
            new ProductUpdated($product)
        ]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $product->setCode($product->getCode());
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $product = $this->generateProduct();
        $product->setCode('');
    }
}