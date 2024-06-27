<?php

namespace Project\Common\ApplicationMessages\Buses;

use Webmozart\Assert\Assert;
use Psr\Container\ContainerInterface;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class EventBus implements MessageBusInterface
{
    private array $bindings;
    private ContainerInterface $container;

    public function __construct(array $bindings, ContainerInterface $container)
    {
        $this->bindings = $bindings;
        $this->container = $container;
    }

    public function dispatch(ApplicationMessageInterface $message): void
    {
        Assert::isInstanceOf($message, Event::class, 'Message must be instance of ' . Event::class);
        if (!isset($this->bindings[$message::class])) {
            return;
        }

        $handler = $this->bindings[$message::class];
        if (is_array($handler)) {
            $this->executeHandlers($message, $handler);
        } else {
            $this->executeHandler($message, $handler);
        }
    }

    private function executeHandlers(Event $event, array $handlers): void
    {
        foreach ($handlers as $handler) {
            $this->executeHandler($event, $handler);
        }
    }

    private function executeHandler(Event $event, string $handler): void
    {
        $handlerObject = $this->container->get($handler);
        if (!is_callable($handlerObject)) {
            throw new \DomainException($handler . ' event handler must be callable');
        }

        call_user_func($handlerObject, $event);
    }

    public function canDispatch(ApplicationMessageInterface $message): bool
    {
        return isset($this->bindings[$message::class]);
    }
}