<?php

namespace Project\Tests\Unit\Modules\Client\Deserializers;

use PHPUnit\Framework\TestCase;
use Project\Tests\Unit\Modules\Helpers\OrderFactory;
use Project\Tests\Unit\Modules\Helpers\OffersFactory;
use Project\Modules\Shopping\Api\Events\Orders\OrderCreated;
use Project\Modules\Shopping\Api\Events\Orders\OrderCompleted;
use Project\Common\ApplicationMessages\Events\SerializedEvent;
use Project\Modules\Client\Adapters\Events\OrderCompletedDeserializer;

class OrderCompletedDeserializerTest extends TestCase
{
    use OffersFactory, OrderFactory;

    public function testCreateDeserializer()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $event = new OrderCompleted($order);
        $serializedEvent = new SerializedEvent($event);
        $deserializer = new OrderCompletedDeserializer($serializedEvent);
        $this->assertSame($order->getClient()->getClient()->getId(), $deserializer->getClientId());
    }

    public function testCreateDeserializerWithAnotherEvent()
    {
        $order = $this->generateOrder([$this->generateOffer()]);
        $event = new OrderCreated($order);
        $serializedEvent = new SerializedEvent($event);
        $this->expectException(\InvalidArgumentException::class);
        new OrderCompletedDeserializer($serializedEvent);
    }
}