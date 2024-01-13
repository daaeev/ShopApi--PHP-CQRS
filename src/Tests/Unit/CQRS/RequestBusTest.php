<?php

namespace Project\Tests\Unit\CQRS;

use Psr\Container\ContainerInterface;
use Project\Tests\Unit\CQRS\Handlers\ServiceHandler;
use Project\Tests\Unit\CQRS\Handlers\CallableHandler;
use Project\Common\ApplicationMessages\Buses\RequestBus;

class RequestBusTest extends \PHPUnit\Framework\TestCase
{
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        parent::setUp();
    }

    public function testDispatchCommandWithCallableHandler()
    {
        $command = new \stdClass;
        $handlerMock = $this->getMockBuilder(CallableHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handlerMock->expects($this->once())
            ->method('__invoke')
            ->with($command)
            ->willReturn('Success');

        $this->container->expects($this->once())
            ->method('get')
            ->with($handlerMock::class)
            ->willReturn($handlerMock);

        $requestBus = new RequestBus([$command::class => $handlerMock::class], $this->container);
        $this->assertSame('Success', $requestBus->dispatch($command));
    }

    public function testDispatchCommandWithServiceHandler()
    {
        $command = new \stdClass;
        $handlerMock = $this->getMockBuilder(ServiceHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handlerMock->expects($this->once())
            ->method('handle')
            ->with($command)
            ->willReturn('Success');

        $this->container->expects($this->once())
            ->method('get')
            ->with($handlerMock::class)
            ->willReturn($handlerMock);

        $requestBus = new RequestBus(
            [$command::class => [$handlerMock::class, 'handle']],
            $this->container
        );
        
        $this->assertSame('Success', $requestBus->dispatch($command));
    }

    public function testDispatchCommandWithoutRegisteredHandlers()
    {
        $command = new \stdClass;
        $requestBus = new RequestBus([], $this->container);
        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testDispatchCommandWithUndefinedCallableHandlerClass()
    {
        $command = new \stdClass;
        $requestBus = new RequestBus([$command::class => 'Undefined'], $this->container);
        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testDispatchCommandWithUndefinedServiceHandlerClass()
    {
        $command = new \stdClass;
        $requestBus = new RequestBus(
            [$command::class => ['Undefined', 'handle']],
            $this->container
        );
        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testDispatchCommandWithUndefinedServiceHandlerMethod()
    {
        $command = new \stdClass;
        $handler = new CallableHandler;

        $this->container->expects($this->once())
            ->method('get')
            ->with(CallableHandler::class)
            ->willReturn($handler);

        $requestBus = new RequestBus(
            [$command::class => [$handler::class, 'undefined']],
            $this->container
        );

        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testDispatchEventWithNonCallableHandler()
    {
        $command = new \stdClass;
        $handler = new \stdClass;

        $this->container->expects($this->once())
            ->method('get')
            ->with($handler::class)
            ->willReturn($handler);

        $requestBus = new RequestBus([$command::class => $handler::class], $this->container);
        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testCanDispatch()
    {
        $command = new \stdClass;
        $handlerMock = new \stdClass;
        $requestBus = new RequestBus([$command::class => $handlerMock::class], $this->container);
        $this->assertTrue($requestBus->canDispatch($command));
    }

    public function testCantDispatch()
    {
        $command = new \stdClass;
        $requestBus = new RequestBus([], $this->container);
        $this->assertFalse($requestBus->canDispatch($command));
    }
}