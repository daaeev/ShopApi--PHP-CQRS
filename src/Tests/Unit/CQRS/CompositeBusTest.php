<?php

namespace Project\Tests\Unit\CQRS;

use DomainException;
use Project\Tests\Unit\CQRS\Commands\Handlers\CallableCommandHandler;
use Project\Tests\Unit\CQRS\Container\TestContainer;
use Project\Common\CQRS\Buses\CompositeBus;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Tests\Unit\CQRS\Commands\CommandsTrait;

class CompositeBusTest extends \PHPUnit\Framework\TestCase
{
    use CommandsTrait;

    public function testDispatch()
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

    public function testCantDispatchWithoutRegisteredBuses()
    {
        $this->expectException(DomainException::class);

        $bus = new CompositeBus;
        $bus->dispatch(new Commands\TestCommand);
    }

    public function testCantDispatchWithRegisteredBuses()
    {
        $this->expectException(DomainException::class);

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
        $bus->dispatch($command);
    }
}