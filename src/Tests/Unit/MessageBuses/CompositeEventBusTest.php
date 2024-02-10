<?php

namespace Project\Tests\Unit\MessageBuses;

use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\Buses\EventBus;
use Project\Common\ApplicationMessages\Buses\CompositeEventBus;

class CompositeEventBusTest extends \PHPUnit\Framework\TestCase
{
    public function testDispatchEvent()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $busMock = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($event)
            ->willReturn(true);

        $busMock->expects($this->once())
            ->method('dispatch')
            ->with($event);

        $compositeBus = new CompositeEventBus;
        $compositeBus->registerBus($busMock);
        $compositeBus->dispatch($event);
    }

    public function testDispatchEventWithOneBusThatCantDispatchEvent()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $busMock = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($event)
            ->willReturn(false);

        $busMock->expects($this->never())
            ->method('dispatch')
            ->with($event);

        $compositeBus = new CompositeEventBus;
        $compositeBus->registerBus($busMock);
        $compositeBus->dispatch($event);
    }

    public function testDispatchEventWithoutRegisteredBusses()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $this->expectNotToPerformAssertions();
        $compositeBus = new CompositeEventBus;
        $compositeBus->dispatch($event);
    }

    public function testCanDispatch()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $busMock = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($event)
            ->willReturn(true);

        $compositeBus = new CompositeEventBus;
        $compositeBus->registerBus($busMock);
        $this->assertTrue($compositeBus->canDispatch($event));
    }

    public function testCantDispatchWithOneRegisteredBus()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $busMock = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($event)
            ->willReturn(false);

        $compositeBus = new CompositeEventBus;
        $compositeBus->registerBus($busMock);
        $this->assertFalse($compositeBus->canDispatch($event));
    }

    public function testCantDispatchWithoutRegisteredBusses()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $compositeBus = new CompositeEventBus;
        $this->assertFalse($compositeBus->canDispatch($event));
    }

    public function testRegisterBus()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $busMock = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($event)
            ->willReturn(true);

        $compositeBus = new CompositeEventBus;
        $this->assertFalse($compositeBus->canDispatch($event));
        $compositeBus->registerBus($busMock);
        $this->assertTrue($compositeBus->canDispatch($event));
    }
}