<?php

namespace Project\Tests\Unit\Events;

use Project\Tests\Unit\Events\Entities\TestEvent;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;

class DispathEventsTraitTest extends \PHPUnit\Framework\TestCase
{
    use DispatchEventsTrait;

    public function testSetDispatcher()
    {
        $dispatcherMock = $this->createMock(MessageBusInterface::class);
        $this->setDispatcher($dispatcherMock);
        $this->assertTrue(isset($this->dispatcher));
        $this->assertSame($dispatcherMock, $this->dispatcher);
    }

    public function testCheckDispatcherInstantiate()
    {
        $this->expectNotToPerformAssertions();
        $dispatcherMock = $this->createMock(MessageBusInterface::class);
        $this->setDispatcher($dispatcherMock);
        $this->checkDispatcherInstantiate();
    }

    public function testCheckDispatcherInstantiateIfDoesNot()
    {
        $this->expectException(\DomainException::class);
        $this->checkDispatcherInstantiate();
    }

    public function testDispatch()
    {
        $event = new TestEvent;
        $dispatcherMock = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with($event);

        $this->setDispatcher($dispatcherMock);
        $this->dispatch($event);
    }

    public function testDispatchIfDispatcherDoesNotInstantiated()
    {
        $this->expectException(\DomainException::class);
        $event = new TestEvent;
        $this->dispatch($event);
    }

    public function testDispatchEvents()
    {
        $events = [new TestEvent, new TestEvent, new TestEvent];
        $dispatcherMock = $this->getMockBuilder(MessageBusInterface::class)->getMock();
        $dispatcherMock->expects($matcher = $this->exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function (Event $event) use ($matcher, $events) {
                match ($matcher->numberOfInvocations()) {
                    1 =>  $this->assertSame($events[0], $event),
                    2 =>  $this->assertSame($events[1], $event),
                    3 =>  $this->assertSame($events[2], $event),
                };
            });

        $this->setDispatcher($dispatcherMock);
        $this->dispatchEvents($events);
    }

    public function testDispatchEventsIfDispatcherDoesNotIntantiated()
    {
        $this->expectException(\DomainException::class);
        $this->dispatchEvents([new TestEvent]);
    }
}