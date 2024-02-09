<?php

namespace Project\Tests\Unit\CQRS;

use Psr\Container\ContainerInterface;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Tests\Unit\CQRS\Handlers\CallableHandler;
use Project\Common\ApplicationMessages\Buses\EventBus;

class EventBusTest extends \PHPUnit\Framework\TestCase
{
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();

        parent::setUp();
    }

    public function testDispatchEvent()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $handlerMock = $this->getMockBuilder(CallableHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handlerMock->expects($this->once())
            ->method('__invoke')
            ->with($event);

        $this->container->expects($this->once())
            ->method('get')
            ->with($handlerMock::class)
            ->willReturn($handlerMock);

        $eventBus = new EventBus([$event::class => $handlerMock::class], $this->container);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventWithManyHandlers()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $firstHandlerMock = $this->getMockBuilder(CallableHandler::class)
            ->getMock();

        $firstHandlerMock->expects($this->once())
            ->method('__invoke')
            ->with($event);

        $secondHandlerMock = $this->getMockBuilder(CallableHandler::class)
            ->getMock();

        $secondHandlerMock->expects($this->once())
            ->method('__invoke')
            ->with($event);

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($firstHandlerMock, $secondHandlerMock);

        $eventBus = new EventBus(
            [$event::class => [$firstHandlerMock::class, $secondHandlerMock::class]],
            $this->container
        );
        $eventBus->dispatch($event);
    }

    public function testDispatchNotEventObject()
    {
        $event = new \stdClass;
        $eventBus = new EventBus([], $this->container);
        $this->expectException(\InvalidArgumentException::class);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventWithoutRegisteredHandlers()
    {
        $this->expectNotToPerformAssertions();
        $event = $this->getMockBuilder(Event::class)->getMock();
        $eventBus = new EventBus([], $this->container);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventWithNonCallableHandler()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $handlerMock = new \stdClass;

        $this->container->expects($this->once())
            ->method('get')
            ->with($handlerMock::class)
            ->willReturn($handlerMock);

        $eventBus = new EventBus([$event::class => $handlerMock::class], $this->container);
        $this->expectException(\DomainException::class);
        $eventBus->dispatch($event);
    }

    public function testCanDispatch()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $handlerMock = new \stdClass;

        $eventBus = new EventBus([$event::class => $handlerMock::class], $this->container);
        $this->assertTrue($eventBus->canDispatch($event));
    }

    public function testCantDispatch()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $eventBus = new EventBus([], $this->container);
        $this->assertFalse($eventBus->canDispatch($event));
    }
}