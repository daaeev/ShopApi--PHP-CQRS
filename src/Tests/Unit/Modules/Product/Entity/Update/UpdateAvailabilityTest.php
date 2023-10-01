<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Project\Common\Product\Availability;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;
use Project\Modules\Catalogue\Api\Events\Product\ProductAvailabilityChanged;

class UpdateAvailabilityTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $product->setAvailability(Availability::PREORDER);
        $this->assertEquals(Availability::PREORDER, $product->getAvailability());
        $this->assertNotEmpty($product->getUpdatedAt());
        $this->assertEvents($product, [
            new ProductAvailabilityChanged($product),
            new ProductUpdated($product),
        ]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $product->setAvailability(Availability::IN_STOCK);
        $this->assertNull($product->getUpdatedAt());
        $this->assertEquals(Availability::IN_STOCK, $product->getAvailability());
        $this->assertEmpty($product->flushEvents());
    }
}