<?php

namespace Project\Tests\Unit\Modules\Product\Entity\Update;

use Webmozart\Assert\InvalidArgumentException;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Modules\Product\Api\Events\ProductUpdated;
use Project\Tests\Unit\Modules\Helpers\AssertEvents;
use Project\Tests\Unit\Modules\Product\Entity\Color\TestColor;
use Project\Tests\Unit\Modules\Product\Entity\Color\OtherTestColor;

class UpdateColorsTest extends \PHPUnit\Framework\TestCase
{
    use ProductFactory, AssertEvents;

    public function testUpdate()
    {
        $product = $this->generateProduct();
        $this->assertEmpty($product->getColors());
        $colors = [
            new TestColor(md5(rand())),
            new TestColor(md5(rand()))
        ];
        $this->assertFalse($product->sameColors($colors));
        $product->setColors($colors);

        $this->assertCount(2, $product->getColors());
        $this->assertTrue($product->sameColors($colors));
        foreach ($colors as $color) {
            $this->assertArrayHasKey($color->getColor(), $product->getColors());
            $this->assertTrue($color->equalsTo($product->getColors()[$color->getColor()]));
        }
        $this->assertEvents($product, [
            new ProductUpdated($product)
        ]);
    }

    public function testUpdateToSame()
    {
        $product = $this->generateProduct();
        $colors = [
            new TestColor(md5(rand())),
            new TestColor(md5(rand()))
        ];
        $product->setColors($colors);
        $product->flushEvents();
        $product->setColors($colors);

        $this->assertCount(2, $product->getColors());
        $this->assertTrue($product->sameColors($colors));
        foreach ($colors as $color) {
            $this->assertArrayHasKey($color->getColor(), $product->getColors());
            $this->assertTrue($color->equalsTo($product->getColors()[$color->getColor()]));
        }
        $this->assertEmpty($product->flushEvents());
    }

    public function testUpdateWithRepeatingColors()
    {
        $product = $this->generateProduct();
        $clonedColor = new TestColor(md5(rand()));
        $colors = [
            $clonedColor,
            clone $clonedColor,
            new TestColor(md5(rand())),
        ];
        $product->setColors($colors);

        $this->assertCount(2, $product->getColors());
        $this->assertFalse($product->sameColors($colors));
        foreach ($colors as $color) {
            $this->assertArrayHasKey($color->getColor(), $product->getColors());
            $this->assertTrue($color->equalsTo($product->getColors()[$color->getColor()]));
        }
        $this->assertEvents($product, [
            new ProductUpdated($product)
        ]);
    }

    public function testUpdateWithInvalidColorsData()
    {
        $this->expectException(InvalidArgumentException::class);
        $product = $this->generateProduct();
        $product->setColors([
            'Invalid color data'
        ]);
    }

    public function testEqualsColors()
    {
        $color = new TestColor(md5(rand()));
        $equalColor = new TestColor($color->getColor());
        $this->assertTrue($color->equalsTo($equalColor));
    }

    public function testDoesNotEqualsColors()
    {
        $color = new TestColor(md5(rand()));
        $this->assertFalse($color->equalsTo(new TestColor(md5(rand()))));
        $this->assertFalse($color->equalsTo(new OtherTestColor($color->getColor())));
    }
}