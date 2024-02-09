<?php

namespace Project\Tests\Unit\Entity;

use Project\Common\Entity\Aggregate;
use Project\Tests\Unit\Events\Helpers\EventsFactory;

class AggregateTest extends \PHPUnit\Framework\TestCase
{
    use EventsFactory;

    private Aggregate $aggregate;

    protected function setUp(): void
    {
        $this->aggregate = new class extends Aggregate {};
        parent::setUp();
    }

    public function testAddEvent()
    {
        $this->assertEmpty($this->aggregate->flushEvents());
        $this->aggregate->addEvent($event = $this->makeEvent());
        $events = $this->aggregate->flushEvents();
        $this->assertCount(1, $events);
        $this->assertSame($event, $events[0]);
        $this->assertEmpty($this->aggregate->flushEvents());
    }

    public function testAddSameEvents()
    {
        $event = $this->makeEvent();
        $this->aggregate->addEvent($event);
        $this->aggregate->addEvent($event);
        $events = $this->aggregate->flushEvents();
        $this->assertCount(1, $events);
    }
}