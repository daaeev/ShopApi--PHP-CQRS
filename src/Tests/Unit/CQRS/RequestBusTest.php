<?php

namespace Project\Tests\Unit\CQRS;

use DomainException;
use Project\Common\CQRS\Buses\RequestBus;
use Project\Tests\Unit\CQRS\Commands\CommandsTrait;
use Project\Tests\Unit\CQRS\Commands\Handlers\CallableCommandHandler;
use Project\Tests\Unit\CQRS\Container\NotFoundException;
use Project\Tests\Unit\CQRS\Container\TestContainer;

class RequestBusTest extends \PHPUnit\Framework\TestCase
{
    use CommandsTrait;

    public function testDispatch()
    {
        $command = new Commands\TestCommand;
        $handlerMock = $this->getMockBuilder(CallableCommandHandler::class)
            ->getMock();

        $handlerMock->expects($this->once())
            ->method('__invoke')
            ->with($command)
            ->willReturn('Success');

        $bus = new RequestBus(
            $this->getCommandBindings(),
            new TestContainer([CallableCommandHandler::class => $handlerMock])
        );

        $this->assertEquals('Success', $bus->dispatch($command));
    }

    public function testDispatchNotBoundedCommand()
    {
        $this->expectException(DomainException::class);

        $bus = new RequestBus(
            [],
            new TestContainer([])
        );

        $bus->dispatch(new Commands\TestCommand);
    }

    public function testCommandNotExistsInContainer()
    {
        $this->expectException(NotFoundException::class);

        $bus = new RequestBus(
            $this->getCommandBindings(),
            new TestContainer([])
        );

        $bus->dispatch(new Commands\TestCommand);
    }

    public function testNonCallableCommandHandler()
    {
        $this->expectException(DomainException::class);

        $bus = new RequestBus(
            [
                Commands\TestCommand::class => Commands\Handlers\NonCallableCommandHandler::class
            ],
            new TestContainer([
                Commands\Handlers\NonCallableCommandHandler::class => new Commands\Handlers\NonCallableCommandHandler
            ])
        );

        $bus->dispatch(new Commands\TestCommand);
    }

    public function testCanDispatch()
    {
        $bus = new RequestBus(
            $this->getCommandBindings(),
            new TestContainer([])
        );

        $this->assertTrue($bus->canDispatch(new Commands\TestCommand));
    }

    public function testCantDispatch()
    {
        $bus = new RequestBus(
            [],
            new TestContainer([])
        );

        $this->assertFalse($bus->canDispatch(new Commands\TestCommand));
    }
}