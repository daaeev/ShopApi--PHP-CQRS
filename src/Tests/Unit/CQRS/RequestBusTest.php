<?php

namespace Project\Tests\Unit\CQRS;

use Project\Common\CQRS\Buses\RequestBus;
use Project\Tests\Unit\CQRS\Commands\Handlers\ServiceCommandHandler;
use Project\Tests\Unit\CQRS\Commands\CommandsTrait;
use Project\Tests\Unit\CQRS\Commands\Handlers\CallableCommandHandler;
use Project\Tests\Unit\CQRS\Container\NotFoundException;
use Project\Tests\Unit\CQRS\Container\TestContainer;

class RequestBusTest extends \PHPUnit\Framework\TestCase
{
    use CommandsTrait;

    public function testDispatchCommandWithCallableHandler()
    {
        $command = new Commands\TestCommand;
        $handler = new CallableCommandHandler;
        $bus = new RequestBus(
            $this->getCommandBindings(),
            new TestContainer([CallableCommandHandler::class => $handler])
        );
        $this->assertEquals('Success', $bus->dispatch($command));
    }

    public function testDispatchCommandWithNonCallableHandler()
    {
        $this->expectException(\DomainException::class);
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

    public function testDispatchCommandWithoutHandler()
    {
        $this->expectException(\DomainException::class);
        $bus = new RequestBus(
            [],
            new TestContainer([])
        );
        $bus->dispatch(new Commands\TestCommand);
    }

    public function testCommandHandlerDoesNotRegisteredInContainer()
    {
        $this->expectException(NotFoundException::class);
        $bus = new RequestBus(
            $this->getCommandBindings(),
            new TestContainer([])
        );
        $bus->dispatch(new Commands\TestCommand);
    }

    public function testDispatchCommandWithServiceMethodHandler()
    {
        $command = new Commands\TestCommand;
        $handler = [ServiceCommandHandler::class, 'handle'];
        $bus = new RequestBus(
            [$command::class => $handler],
            new TestContainer([ServiceCommandHandler::class => new ServiceCommandHandler])
        );
        $this->assertEquals('Success', $bus->dispatch($command));
    }

    public function testDispatchCommandWithUndefinedServiceMethodHandler()
    {
        $this->expectException(\DomainException::class);
        $command = new Commands\TestCommand;
        $handler = [ServiceCommandHandler::class, 'undefined method'];
        $bus = new RequestBus(
            [$command::class => $handler],
            new TestContainer([ServiceCommandHandler::class => new ServiceCommandHandler])
        );
        $bus->dispatch($command);
    }

    public function testServiceHandlerDoesNotRegisteredInContainer()
    {
        $this->expectException(NotFoundException::class);
        $command = new Commands\TestCommand;
        $handler = [ServiceCommandHandler::class, 'handle'];
        $bus = new RequestBus(
            [$command::class => $handler],
            new TestContainer([])
        );
        $bus->dispatch($command);
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