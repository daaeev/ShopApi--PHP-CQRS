<?php

namespace Project\Common\ApplicationMessages\Buses;

use Webmozart\Assert\Assert;
use Psr\Container\ContainerInterface;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\Events\SerializedEvent;
use Project\Common\ApplicationMessages\Events\RegisteredConsumer;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class EventBus implements MessageBusInterface
{
    public function __construct(
        private readonly array $bindings,
        private readonly ContainerInterface $container,
    ) {
        $this->guardAllIsInstanceOfRegisteredConsumer($bindings);
    }

    private function guardAllIsInstanceOfRegisteredConsumer(array $bindings): void
    {
        foreach ($bindings as $consumer) {
            if (is_array($consumer)) {
                $this->guardAllIsInstanceOfRegisteredConsumer($consumer);
            } else {
                Assert::isInstanceOf($consumer, RegisteredConsumer::class);
            }
        }
    }

    public function dispatch(ApplicationMessageInterface $message): void
    {
        Assert::isInstanceOf($message, Event::class, 'Message must be instance of ' . Event::class);
        $serializedEvent = new SerializedEvent($message);
        if (!isset($this->bindings[$serializedEvent->getEventId()])) {
            return;
        }

        $consumer = $this->bindings[$serializedEvent->getEventId()];
        if (is_array($consumer)) {
            $this->executeConsumers($serializedEvent, $consumer);
        } else {
            $this->executeConsumer($serializedEvent, $consumer);
        }
    }

    private function executeConsumers(SerializedEvent $event, array $consumers): void
    {
        foreach ($consumers as $consumer) {
            $this->executeConsumer($event, $consumer);
        }
    }

    private function executeConsumer(SerializedEvent $event, RegisteredConsumer $registeredConsumer): void
    {
        if (!class_exists($registeredConsumer->consumer)) {
            throw new \DomainException("$registeredConsumer->consumer event consumer class not found");
        }

        $wrappedSerializedEvent = $this->wrapSerializedEvent($event, $registeredConsumer->deserializer);
        $consumer = $this->container->get($registeredConsumer->consumer);
        if (!is_callable($consumer)) {
            throw new \DomainException("$registeredConsumer->consumer event consumer must be callable");
        }

        call_user_func($consumer, $wrappedSerializedEvent);
    }

    private function wrapSerializedEvent(SerializedEvent $event, ?string $deserializer): object
    {
        if (null === $deserializer) {
            return $event;
        }

        if (!class_exists($deserializer)) {
            throw new \DomainException("$deserializer event deserializer class not found");
        }

        return new $deserializer($event);
    }

    public function canDispatch(ApplicationMessageInterface $message): bool
    {
        Assert::isInstanceOf($message, Event::class, 'Message must be instance of ' . Event::class);
        return isset($this->bindings[$message->getEventId()]);
    }
}