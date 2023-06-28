<?php

namespace Project\Tests\Unit\CQRS;

use Project\Common\CQRS\Buses\CompositeEventBus;
use Project\Common\CQRS\Buses\EventBus;

class CompositeEventBusTest extends \PHPUnit\Framework\TestCase
{
    public function testDispatchEvent()
    {
        $command = new Commands\TestCommand;
        $busMock = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);
        $busMock->expects($this->once())
            ->method('dispatch')
            ->with($command);

        $bus = new CompositeEventBus;
        $bus->registerBus($busMock);
        $bus->dispatch($command);
    }

    public function testDispatchEventWithoutAnyHandlers()
    {
        $this->expectNotToPerformAssertions();
        $bus = new CompositeEventBus;
        $bus->dispatch(new Commands\TestCommand);
    }

    public function testCanDispatch()
    {
        $command = new Commands\TestCommand;
        $busMock = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);

        $bus = new CompositeEventBus;
        $bus->registerBus($busMock);
        $this->assertTrue($bus->canDispatch($command));
    }

    public function testCantDispatch()
    {
        $command = new Commands\TestCommand;
        $busMock = $this->getMockBuilder(EventBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(false);

        $bus = new CompositeEventBus;
        $bus->registerBus($busMock);
        $this->assertFalse($bus->canDispatch($command));
    }

    public function testCantDispatchWithoutAnyRegisteredBuses()
    {
        $command = new Commands\TestCommand;
        $bus = new CompositeEventBus;
        $this->assertFalse($bus->canDispatch($command));
    }
}