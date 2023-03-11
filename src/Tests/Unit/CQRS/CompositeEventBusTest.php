<?php

namespace Project\Tests\Unit\CQRS;

use Project\Common\CQRS\Buses\CompositeEventBus;
use Project\Common\CQRS\Buses\EventBus;

class CompositeEventBusTest extends \PHPUnit\Framework\TestCase
{
    public function testDiscpatch()
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

    public function testCantDispatchWithoutRegisteredBuses()
    {
        $bus = new CompositeEventBus;

        try {
            $bus->dispatch(new Commands\TestCommand);
            $this->assertTrue(true);
        } catch (\Throwable) {
            $this->fail('Exception thrown');
        }
    }

    public function testCantDispatchWithRegisteredBuses()
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

        try {
            $bus->dispatch(new Commands\TestCommand);
            $this->assertTrue(true);
        } catch (\Throwable) {
            $this->fail('Exception thrown');
        }
    }
}