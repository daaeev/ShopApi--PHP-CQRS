<?php

namespace Project\Tests\Unit\Modules\Product\Entity;

use DomainException;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Api\Events\ProductDeleted;
use Project\Tests\Unit\Modules\Helpers\AssertEventsTrait;

class DeleteTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEventsTrait;

    public function testDelete()
    {
        $product = $this->generateProduct();
        $product->deactivate();
        $product->flushEvents();
        $product->delete();
        $this->assertEvents($product, [new ProductDeleted($product)]);
    }

    public function testDeleteIfActive()
    {
        $this->expectException(DomainException::class);
        $product = $this->generateProduct();
        $product->delete();
    }
}