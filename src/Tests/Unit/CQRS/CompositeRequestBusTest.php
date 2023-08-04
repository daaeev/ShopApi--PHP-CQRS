<?php

namespace Project\Tests\Unit\CQRS;

use Project\Common\CQRS\Buses\RequestBus;
use Project\Common\CQRS\Buses\CompositeRequestBus;
use Project\Tests\Unit\CQRS\Commands\CommandsTrait;

class CompositeRequestBusTest extends \PHPUnit\Framework\TestCase
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

        $bus = new CompositeRequestBus;
        $bus->registerBus($busMock);
        $this->assertEquals('Success', $bus->dispatch($command));
    }

    public function testDispatchCommandWithoutAnyHandlers()
    {
        $this->expectException(\DomainException::class);
        $bus = new CompositeRequestBus;
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

        $bus = new CompositeRequestBus;
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

        $bus = new CompositeRequestBus;
        $bus->registerBus($busMock);
        $this->assertFalse($bus->canDispatch($command));
    }

    public function testCantDispatchWithoutAnyRegisteredBuses()
    {
        $command = new Commands\TestCommand;
        $bus = new CompositeRequestBus;
        $this->assertFalse($bus->canDispatch($command));
    }
}