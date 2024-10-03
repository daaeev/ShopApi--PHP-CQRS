<?php

namespace Project\Tests\Unit\MessageBuses;

use Psr\Container\ContainerInterface;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\Buses\EventBus;
use Project\Common\ApplicationMessages\Events\SerializedEvent;
use Project\Common\ApplicationMessages\Events\RegisteredConsumer;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;
use Project\Tests\Unit\MessageBuses\Handlers\TestEventDeserializer;
use Project\Tests\Unit\MessageBuses\Handlers\ConsumerWithDeserializer;
use Project\Tests\Unit\MessageBuses\Handlers\ConsumerWithoutDeserializer;

class EventBusTest extends \PHPUnit\Framework\TestCase
{
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->getMockBuilder(ContainerInterface::class)->getMock();
    }

    public function testCreateBusWithCorrectBindings(): void
    {
        $this->expectNotToPerformAssertions();
        new EventBus([
            'testEvent1' => new RegisteredConsumer('ConsumerClass', 'Deserializer'),
            'testEvent2' => [
                new RegisteredConsumer('ConsumerClass', 'Deserializer'),
                new RegisteredConsumer('ConsumerClass', 'Deserializer'),
            ],
        ], $this->container);
    }

    public function testCreateBusWithIncorrectBindings(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new EventBus([1, 2, 3], $this->container);
    }

    public function testDispatchEventWithoutDeserializer()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId = 'test');

        $event->expects($this->once())
            ->method('getData')
            ->willReturn(['test' => true]);

        $consumer = $this->getMockBuilder(ConsumerWithoutDeserializer::class)->getMock();
        $consumer->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (SerializedEvent $serializedEvent) use ($eventId) {
                $this->assertSame($serializedEvent->getEventId(), $eventId);
                $this->assertTrue($serializedEvent->test);
                return true;
            }));

        $this->container->expects($this->once())
            ->method('get')
            ->with(ConsumerWithoutDeserializer::class)
            ->willReturn($consumer);

        $registeredConsumer = new RegisteredConsumer(ConsumerWithoutDeserializer::class);
        $eventBus = new EventBus([$eventId => $registeredConsumer], $this->container);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventWithDeserializer()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId = 'test');

        $event->expects($this->once())
            ->method('getData')
            ->willReturn(['test' => true]);

        $consumer = $this->getMockBuilder(ConsumerWithDeserializer::class)->getMock();
        $consumer->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(function (TestEventDeserializer $deserializer) use ($eventId) {
                $this->assertSame($deserializer->event->getEventId(), $eventId);
                $this->assertTrue($deserializer->event->test);
                return true;
            }));

        $this->container->expects($this->once())
            ->method('get')
            ->with(ConsumerWithDeserializer::class)
            ->willReturn($consumer);

        $registeredConsumer = new RegisteredConsumer(
            ConsumerWithDeserializer::class,
            TestEventDeserializer::class
        );

        $eventBus = new EventBus([$eventId => $registeredConsumer], $this->container);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventWithManyConsumers()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId = 'test');

        $event->expects($this->once())
            ->method('getData')
            ->willReturn(['test' => true]);

        $consumer = $this->getMockBuilder(ConsumerWithoutDeserializer::class)->getMock();
        $consumer->expects($this->exactly(2))->method('__invoke');

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->with(ConsumerWithoutDeserializer::class)
            ->willReturn($consumer);

        $registeredConsumer = new RegisteredConsumer(ConsumerWithoutDeserializer::class);
        $eventBus = new EventBus([$eventId => [$registeredConsumer, $registeredConsumer]], $this->container);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventWithoutRegisteredConsumers()
    {
        $this->expectNotToPerformAssertions();
        $event = $this->getMockBuilder(Event::class)->getMock();
        $eventBus = new EventBus([], $this->container);
        $eventBus->dispatch($event);
    }

    public function testDispatchNotEventObject()
    {
        $event = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $eventBus = new EventBus([], $this->container);
        $this->expectException(\InvalidArgumentException::class);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventIfConsumerClassDoesNotExists()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId = 'test');

        $event->expects($this->once())
            ->method('getData')
            ->willReturn(['test' => true]);

        $registeredConsumer = new RegisteredConsumer(
            'UndefinedClass',
            TestEventDeserializer::class
        );

        $eventBus = new EventBus([$eventId => $registeredConsumer], $this->container);
        $this->expectException(\DomainException::class);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventIfDeserializerClassDoesNotExists()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId = 'test');

        $event->expects($this->once())
            ->method('getData')
            ->willReturn(['test' => true]);

        $registeredConsumer = new RegisteredConsumer(
            ConsumerWithDeserializer::class,
            'UndefinedClass'
        );

        $eventBus = new EventBus([$eventId => $registeredConsumer], $this->container);
        $this->expectException(\DomainException::class);
        $eventBus->dispatch($event);
    }

    public function testDispatchEventIfConsumerNotCallable()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId = 'test');

        $event->expects($this->once())
            ->method('getData')
            ->willReturn(['test' => true]);

        $consumer = $this->getMockBuilder(\stdClass::class)->getMock();
        $this->container->expects($this->once())
            ->method('get')
            ->with(\stdClass::class)
            ->willReturn($consumer);

        $registeredConsumer = new RegisteredConsumer(\stdClass::class);
        $eventBus = new EventBus([$eventId => $registeredConsumer], $this->container);
        $this->expectException(\DomainException::class);
        $eventBus->dispatch($event);
    }

    public function testCanDispatch()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())
            ->method('getEventId')
            ->willReturn($eventId = 'test');

        $consumer = new RegisteredConsumer(ConsumerWithoutDeserializer::class);
        $eventBus = new EventBus([$eventId => $consumer], $this->container);
        $this->assertTrue($eventBus->canDispatch($event));
    }

    public function testCantDispatch()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())
            ->method('getEventId')
            ->willReturn('test');

        $eventBus = new EventBus([], $this->container);
        $this->assertFalse($eventBus->canDispatch($event));
    }

    public function testCanDispatchIfProvidedNotInstanceOfEvent()
    {
        $event = $this->getMockBuilder(ApplicationMessageInterface::class)->getMock();
        $eventBus = new EventBus([], $this->container);
        $this->expectException(\InvalidArgumentException::class);
        $eventBus->canDispatch($event);
    }
}