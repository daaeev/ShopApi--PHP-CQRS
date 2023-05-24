<?php

namespace Project\Tests\Unit\Events;

use Project\Common\Events\EventTrait;
use Project\Tests\Unit\Events\Helpers\EventsFactory;

class EventTraitTest extends \PHPUnit\Framework\TestCase
{
    use EventTrait, EventsFactory;

    public function testAddEvent()
    {
        $this->assertEmpty($this->events);
        $events = [
            $this->makeEvent(),
            $this->makeEvent(),
            $this->makeEvent(),
        ];
        array_map([$this, 'addEvent'], $events);
        $this->assertNotEmpty($this->events);
        $this->assertCount(3, $this->events);
        $this->assertSame($events, $this->events);
        $this->assertSame($events[0], $this->events[0]);
        $this->assertSame($events[1], $this->events[1]);
        $this->assertSame($events[2], $this->events[2]);
    }

    public function testFlushEvents()
    {
        $this->assertEmpty($this->events);
        $events = [
            $this->makeEvent(),
            $this->makeEvent(),
            $this->makeEvent(),
        ];
        array_map([$this, 'addEvent'], $events);
        $this->assertNotEmpty($this->events);
        $this->assertCount(3, $this->events);
        $flushedEvents = $this->flushEvents();
        $this->assertEmpty($this->events);
        $this->assertNotEmpty($flushedEvents);
        $this->assertCount(3, $flushedEvents);
        $this->assertSame($events, $flushedEvents);
        $this->assertSame($events[0], $flushedEvents[0]);
        $this->assertSame($events[1], $flushedEvents[1]);
        $this->assertSame($events[2], $flushedEvents[2]);
    }
}