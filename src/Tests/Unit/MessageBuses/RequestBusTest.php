<?php

namespace Project\Tests\Unit\MessageBuses;

use Psr\Container\ContainerInterface;
use Project\Tests\Unit\MessageBuses\Handlers\ServiceHandler;
use Project\Tests\Unit\MessageBuses\Handlers\CallableHandler;
use Project\Common\ApplicationMessages\Buses\RequestBus;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class RequestBusTest extends \PHPUnit\Framework\TestCase
{
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder(ContainerInterface::class)->getMock();
    }

    public function testDispatchCommandWithCallableHandler()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
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

    public function testDispatchCommandWithServiceMethodHandler()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
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
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $requestBus = new RequestBus([], $this->container);
        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testDispatchCommandWithUndefinedCallableHandler()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $requestBus = new RequestBus([$command::class => 'Undefined'], $this->container);
        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testDispatchCommandWithUndefinedServiceClass()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $requestBus = new RequestBus([$command::class => ['Undefined', 'handle']], $this->container);
        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testDispatchCommandWithUndefinedServiceMethod()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $handler = new ServiceHandler;
        $requestBus = new RequestBus([$command::class => [$handler::class, 'undefined']], $this->container);
        $this->expectException(\DomainException::class);
        $requestBus->dispatch($command);
    }

    public function testCanDispatch()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $handler = new CallableHandler;
        $requestBus = new RequestBus([$command::class => $handler::class], $this->container);
        $this->assertTrue($requestBus->canDispatch($command));
    }

    public function testCantDispatch()
    {
        $command = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $requestBus = new RequestBus([], $this->container);
        $this->assertFalse($requestBus->canDispatch($command));
    }
}