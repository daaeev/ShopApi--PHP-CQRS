<?php

namespace Project\Common\ApplicationMessages\Buses;

use Psr\Container\ContainerInterface;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class RequestBus implements MessageBusInterface
{
    private array $bindings;
    private ContainerInterface $container;

    public function __construct(array $bindings, ContainerInterface $container)
    {
        $this->bindings = $bindings;
        $this->container = $container;
    }

    public function dispatch(ApplicationMessageInterface $message): mixed
    {
        if (!$this->canDispatch($message)) {
            throw new \DomainException('Cant dispatch message ' . $message::class);
        }

        $handler = $this->bindings[$message::class];
        if (is_string($handler) && class_exists($handler)) {
            $handlerObject = $this->container->get($handler);
            return call_user_func($handlerObject, $message);
        }

        if (is_array($handler) && (count($handler) === 2)) {
            if (!class_exists($handler[0])) {
                throw new \DomainException('Handler class does not exists');
            }

            if (!method_exists($handler[0], $handler[1])) {
                throw new \DomainException("Handler does not have '$handler[1]' method");
            }

            $handlerObject = $this->container->get($handler[0]);
            $handlerMethod = $handler[1];
            return call_user_func([$handlerObject, $handlerMethod], $message);
        }

        throw new \DomainException('Cant handle ' . $message::class . ' message');
    }

    public function canDispatch(ApplicationMessageInterface $message): bool
    {
        return isset($this->bindings[$message::class]);
    }
}