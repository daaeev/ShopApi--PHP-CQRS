<?php

namespace Project\Tests\Unit\Entity;

use Project\Common\Entity\Aggregate;
use Project\Common\ApplicationMessages\Events\Event;

class AggregateTest extends \PHPUnit\Framework\TestCase
{
    private Aggregate $aggregate;
    private Event $event;

    protected function setUp(): void
    {
        $this->aggregate = new class extends Aggregate {};
        $this->event = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testAddEvent()
    {
        $this->aggregate->addEvent($this->event);
        $events = $this->aggregate->flushEvents();
        $this->assertCount(1, $events);
        $this->assertSame($this->event, $events[0]);
        $this->assertEmpty($this->aggregate->flushEvents());
    }

    public function testAddSameEvents()
    {
        $this->aggregate->addEvent($this->event);
        $this->aggregate->addEvent($this->event);
        $events = $this->aggregate->flushEvents();
        $this->assertCount(1, $events);
    }
}