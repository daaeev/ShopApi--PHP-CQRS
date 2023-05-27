<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Project\Modules\Product\Entity\Availability;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Api\Events\ProductUpdated;
use Project\Tests\Unit\Modules\Helpers\AssertEventsTrait;
use Project\Modules\Product\Api\Events\ProductCodeChanged;
use Project\Modules\Product\Api\Events\ProductAvailabilityChanged;

class UpdateAvailabilityTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEventsTrait;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $this->assertEquals(Availability::IN_STOCK, $product->getAvailability());
        $product->setAvailability(Availability::PREORDER);
        $this->assertEquals(Availability::PREORDER, $product->getAvailability());
        $this->assertEvents($product, [
            new ProductAvailabilityChanged($product),
            new ProductUpdated($product),
        ]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $this->assertEquals(Availability::IN_STOCK, $product->getAvailability());
        $product->setAvailability(Availability::IN_STOCK);
        $this->assertEquals(Availability::IN_STOCK, $product->getAvailability());
        $this->assertEmpty($product->flushEvents());
    }
}