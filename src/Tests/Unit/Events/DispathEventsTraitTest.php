<?php

namespace Project\Tests\Unit\Events;

use Project\Common\ApplicationMessages\Events\Event;
use Project\Tests\Unit\Events\Helpers\EventsFactory;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;

class DispathEventsTraitTest extends \PHPUnit\Framework\TestCase
{
    use DispatchEventsTrait, EventsFactory;

    public function testSetDispatcher()
    {
        $this->assertFalse(isset($this->dispatcher));
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
        $event = $this->makeEvent();
        $dispatcherMock = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();

        $dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with($event);

        $this->setDispatcher($dispatcherMock);
        $this->dispatch($event);
    }

    public function testDispatchIfDispatcherDoesNotIntantiate()
    {
        $this->expectException(\DomainException::class);
        $event = $this->makeEvent();
        $this->dispatch($event);
    }

    public function testDispatchEvents()
    {
        $events = [
            $this->makeEvent(),
            $this->makeEvent(),
            $this->makeEvent(),
        ];
        $dispatcherMock = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();
        $matcher = $this->exactly(3);
        $dispatcherMock->expects($matcher)
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

    public function testDispatchEventsIfDispatcherDoesNotIntantiate()
    {
        $this->expectException(\DomainException::class);
        $this->dispatchEvents([]);
    }

    public function testDispatchEventsWithEmptyArray()
    {
        $dispatcherMock = $this->getMockBuilder(MessageBusInterface::class)
            ->getMock();
        $dispatcherMock->expects($this->never())
            ->method('dispatch');
        $this->setDispatcher($dispatcherMock);
        $this->dispatchEvents([]);
    }

    public function testDispatchEventsWithEmptyArrayIfDispathcerDoesNotInstantiate()
    {
        $this->expectException(\DomainException::class);
        $this->dispatchEvents([]);
    }
}