<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Product\Api\Events\ProductUpdated;

class UpdateNameTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $initialName = $product->getName();
        $product->setName('Updated name');
        $this->assertEquals('Updated name', $product->getName());
        $this->assertNotEquals($initialName, $product->getName());
        $this->assertEvents($product, [new ProductUpdated($product)]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $product->setName($product->getName());
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateToEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $product = $this->generateProduct();
        $product->setName('');
    }
}