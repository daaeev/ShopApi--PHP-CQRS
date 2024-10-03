<?php

namespace Project\Tests\Unit\Events;

use Project\Tests\Unit\Events\Entities\TestEvent;

class EventTest extends \PHPUnit\Framework\TestCase
{
    public function testEventToArray()
    {
        $event = new TestEvent;
        $this->assertSame([
            'id' => $event->getEventId(),
            'data' => $event->getData(),
        ], $event->toArray());
    }
}