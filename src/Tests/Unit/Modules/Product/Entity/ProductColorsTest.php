<?php

namespace Project\Tests\Unit\Modules\Product\Entity;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;

class ProductColorsTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $colors = [uniqid(), uniqid()];

        $product->setColors($colors);

        $this->assertSame($colors, $product->getColors());
        $this->assertNotEmpty($product->getUpdatedAt());
        $this->assertEvents($product, [new ProductUpdated($product)]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $colors = [uniqid(), uniqid()];
        $product->setColors($colors);
        $oldUpdatedAt = $product->getUpdatedAt();
        $product->flushEvents();

        $product->setColors($colors);

        $this->assertSame($oldUpdatedAt, $product->getUpdatedAt());
        $this->assertSame($colors, $product->getColors());
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateWithRepeatingColors()
    {
        $product = $this->generateProduct();
        $clonedColor = uniqid();
        $colors = [$clonedColor, $clonedColor, uniqid()];
        $product->setColors($colors);

        $this->assertCount(2, $product->getColors());
        foreach ($colors as $color) {
            $this->assertTrue(in_array($color, $product->getColors()));
        }

        $this->assertEvents($product, [new ProductUpdated($product)]);
    }
}
