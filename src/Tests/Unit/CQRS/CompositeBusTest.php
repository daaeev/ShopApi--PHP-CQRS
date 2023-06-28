<?php

namespace Project\Tests\Unit\CQRS;

use Project\Common\CQRS\Buses\RequestBus;
use Project\Common\CQRS\Buses\CompositeBus;
use Project\Tests\Unit\CQRS\Commands\CommandsTrait;

class CompositeBusTest extends \PHPUnit\Framework\TestCase
{
    use CommandsTrait;

    public function testDispatchCommand()
    {
        $command = new Commands\TestCommand;
        $busMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);
        $busMock->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn('Success');

        $bus = new CompositeBus;
        $bus->registerBus($busMock);
        $this->assertEquals('Success', $bus->dispatch($command));
    }

    public function testDispatchCommandWithoutAnyHandlers()
    {
        $this->expectException(\DomainException::class);
        $bus = new CompositeBus;
        $bus->dispatch(new Commands\TestCommand);
    }


    public function testCanDispatch()
    {
        $command = new Commands\TestCommand;
        $busMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(true);

        $bus = new CompositeBus;
        $bus->registerBus($busMock);
        $this->assertTrue($bus->canDispatch($command));
    }

    public function testCantDispatch()
    {
        $command = new Commands\TestCommand;
        $busMock = $this->getMockBuilder(RequestBus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $busMock->expects($this->once())
            ->method('canDispatch')
            ->with($command)
            ->willReturn(false);

        $bus = new CompositeBus;
        $bus->registerBus($busMock);
        $this->assertFalse($bus->canDispatch($command));
    }

    public function testCantDispatchWithoutAnyRegisteredBuses()
    {
        $command = new Commands\TestCommand;
        $bus = new CompositeBus;
        $this->assertFalse($bus->canDispatch($command));
    }
}