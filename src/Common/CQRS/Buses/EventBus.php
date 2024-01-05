<?php

namespace Project\Common\CQRS\Buses;

use Webmozart\Assert\Assert;
use Project\Common\Events\Event;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventBus implements MessageBusInterface, EventDispatcherInterface
{
    private array $bindings;
    private ContainerInterface $container;

    public function __construct(array $bindings, ContainerInterface $container)
    {
        $this->bindings = $bindings;
        $this->container = $container;
    }

    public function dispatch(object $event): void
    {
        Assert::isInstanceOf($event, Event::class, 'Event object must be instance of Event');

        if (!isset($this->bindings[$event::class])) {
            return;
        }

        $eventHandler = $this->bindings[$event::class];
        if (is_array($eventHandler)) {
            $this->executeHandlers($event, $eventHandler);
        } else {
            $this->executeHandler($event, $this->container->get($eventHandler));
        }
    }

    private function executeHandlers(object $event, array $handlers): void
    {
        foreach ($handlers as $handler) {
            $this->executeHandler($event, $this->container->get($handler));
        }
    }

    private function executeHandler(object $event, object $handler): void
    {
        if (!is_callable($handler)) {
            throw new \DomainException($event::class . ' handler not callable');
        }

        call_user_func($handler, $event);
    }

    public function canDispatch(object $event): bool
    {
        return isset($this->bindings[$event::class]);
    }
}