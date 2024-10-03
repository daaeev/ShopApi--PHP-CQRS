<?php

namespace Project\Tests\Unit\Events;

use Project\Tests\Unit\Events\Entities\TestEvent;
use Project\Common\ApplicationMessages\Events\SerializedEvent;

class SerializedEventTest extends \PHPUnit\Framework\TestCase
{
    public function testSerializedEvent()
    {
        $event = new TestEvent;
        $serializedEvent = new SerializedEvent($event);
        $this->assertSame($event->getEventId(), $serializedEvent->getEventId());
        $this->assertSame($event->getData()['test'], $serializedEvent->test);

        $this->expectException(\InvalidArgumentException::class);
        $serializedEvent->undefinedProperty;
    }
}