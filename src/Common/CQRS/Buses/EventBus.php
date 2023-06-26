<?php

namespace Project\Common\CQRS\Buses;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventBus implements Interfaces\RequestBus, EventDispatcherInterface
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
        if (!$this->canDispatch($event)) {
            throw new \DomainException('Cant dispatch event ' . $event::class);
        }

        if (is_array($this->bindings[$event::class])) {
            $this->executeHandlers($event);
        } else {
            $this->executeHandler($event);
        }
    }

    public function canDispatch($event): bool
    {
        return isset($this->bindings[$event::class]);
    }

    private function executeHandlers($event): void
    {
        foreach ($this->bindings[$event::class] as $handler) {
            $this->runHandler($event, $this->container->get($handler));
        }
    }

    private function executeHandler($event): void
    {
        $this->runHandler($event, $this->container->get($this->bindings[$event::class]));
    }

    private function runHandler($event, $handler): void
    {
        if (is_callable($handler)) {
            call_user_func($handler, $event);
        } else {
            throw new \DomainException('Cant execute ' . $handler::class . ' event handler');
        }
    }
}