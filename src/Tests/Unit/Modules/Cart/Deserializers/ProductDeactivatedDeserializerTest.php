<?php

namespace Project\Tests\Unit\Modules\Cart\Deserializers;

use PHPUnit\Framework\TestCase;
use Project\Common\Product\Availability;
use Project\Tests\Unit\Modules\Helpers\ProductFactory;
use Project\Common\ApplicationMessages\Events\SerializedEvent;
use Project\Modules\Catalogue\Api\Events\Product\ProductCreated;
use Project\Modules\Catalogue\Api\Events\Product\ProductActivityChanged;
use Project\Modules\Shopping\Adapters\Events\ProductDeactivatedDeserializer;
use Project\Modules\Catalogue\Api\Events\Product\ProductAvailabilityChanged;

class ProductDeactivatedDeserializerTest extends TestCase
{
    use ProductFactory;

    public function testActivityChangedDeserializer()
    {
        $product = $this->generateProduct();
        $event = new ProductActivityChanged($product);
        $serializedEvent = new SerializedEvent($event);
        $deserializer = new ProductDeactivatedDeserializer($serializedEvent);
        $this->assertTrue($deserializer->activityChanged());
        $this->assertSame($product->getId()->getId(), $deserializer->getProductId());
        $this->assertSame($product->isActive(), $deserializer->isProductActive());
        $this->assertSame($product->getAvailability()  !== Availability::OUT_STOCK, $deserializer->isProductAvailable());
    }

    public function testAvailabilityChangedDeserializer()
    {
        $product = $this->generateProduct();
        $event = new ProductAvailabilityChanged($product);
        $serializedEvent = new SerializedEvent($event);
        $deserializer = new ProductDeactivatedDeserializer($serializedEvent);
        $this->assertFalse($deserializer->activityChanged());
        $this->assertSame($product->getId()->getId(), $deserializer->getProductId());
        $this->assertSame($product->isActive(), $deserializer->isProductActive());
        $this->assertSame($product->getAvailability()  !== Availability::OUT_STOCK, $deserializer->isProductAvailable());
    }

    public function testCreateDeserializerWithAnotherEvent()
    {
        $product = $this->generateProduct();
        $event = new ProductCreated($product);
        $serializedEvent = new SerializedEvent($event);
        $this->expectException(\InvalidArgumentException::class);
        new ProductDeactivatedDeserializer($serializedEvent);
    }
}