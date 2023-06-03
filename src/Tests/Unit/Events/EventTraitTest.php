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
        ];
        array_map([$this, 'addEvent'], $events);
        $this->assertNotEmpty($this->events);
        $this->assertCount(1, $this->events);
        $this->assertSame($events, $this->events);
        $this->assertSame($events[0], $this->events[0]);
    }

    public function testFlushEvents()
    {
        $this->assertEmpty($this->events);
        $events = [
            $this->makeEvent(),
        ];
        array_map([$this, 'addEvent'], $events);
        $this->assertNotEmpty($this->events);
        $this->assertCount(1, $this->events);
        $flushedEvents = $this->flushEvents();
        $this->assertEmpty($this->events);
        $this->assertNotEmpty($flushedEvents);
        $this->assertCount(1, $flushedEvents);
        $this->assertSame($events, $flushedEvents);
        $this->assertSame($events[0], $flushedEvents[0]);
    }

    public function testAddSameEvents()
    {
        $this->assertEmpty($this->events);
        $events = [
            $this->makeEvent(),
            $this->makeEvent(),
            $this->makeEvent(),
        ];
        array_map([$this, 'addEvent'], $events);
        $this->assertNotEmpty($this->events);
        $this->assertCount(1, $this->events);
        $this->assertNotSame($events, $this->events);
        $this->assertSame($events[0], $this->events[0]);
    }
}