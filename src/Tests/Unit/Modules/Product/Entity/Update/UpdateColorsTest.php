<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Catalogue\Api\Events\Product\ProductUpdated;

class UpdateColorsTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $colors = [
            md5(rand()),
            md5(rand()),
        ];

        $this->assertFalse($product->sameColors($colors));
        $product->setColors($colors);
        $this->assertTrue($product->sameColors($colors));

        $this->assertCount(2, $product->getColors());
        $this->assertSame($colors, $product->getColors());
        $this->assertNotEmpty($product->getUpdatedAt());
        $this->assertEvents($product, [
            new ProductUpdated($product)
        ]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $colors = [
            md5(rand()),
            md5(rand()),
        ];
        $product->setColors($colors);
        $product->flushEvents();
        $product->setColors($colors);

        $this->assertCount(2, $product->getColors());
        $this->assertTrue($product->sameColors($colors));
        $this->assertSame($colors, $product->getColors());
        $this->assertNotEmpty($product->getUpdatedAt());
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateWithRepeatingColors()
    {
        $product = $this->generateProduct();
        $clonedColor = md5(rand());
        $colors = [
            $clonedColor,
            $clonedColor,
            md5(rand()),
        ];
        $product->setColors($colors);

        $this->assertCount(2, $product->getColors());
        $this->assertFalse($product->sameColors($colors));
        foreach ($colors as $color) {
            $this->assertTrue(in_array($color, $product->getColors()));
        }
        $this->assertEvents($product, [
            new ProductUpdated($product)
        ]);
    }
}