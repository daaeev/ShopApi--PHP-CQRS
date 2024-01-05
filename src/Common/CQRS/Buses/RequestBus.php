<?php

namespace Project\Common\CQRS\Buses;

use Psr\Container\ContainerInterface;

class RequestBus implements MessageBusInterface
{
    private array $bindings;
    private ContainerInterface $container;

    public function __construct(array $bindings, ContainerInterface $container)
    {
        $this->bindings = $bindings;
        $this->container = $container;
    }

    public function dispatch(object $request): mixed
    {
        if (!$this->canDispatch($request)) {
            throw new \DomainException('Cant dispatch command ' . $request::class);
        }

        $handler = $this->bindings[$request::class];
        if (is_string($handler) && class_exists($handler)) {
            $handlerObject = $this->container->get($handler);
            return $this->executeHandler($request, $handlerObject);
        }

        if (is_array($handler) && (count($handler) === 2)) {
            $handlerObject = $this->container->get($handler[0]);
            $handlerMethod = $handler[1];
            return $this->executeHandler($request, [$handlerObject, $handlerMethod]);
        }

        throw new \DomainException('Cant execute ' . $request::class . ' command handler');
    }

    private function executeHandler(object $request, object|array $handler): mixed
    {
        if (!is_callable($handler)) {
            throw new \DomainException($request::class . ' handler not callable');
        }

        return call_user_func($handler, $request);
    }

    public function canDispatch(object $request): bool
    {
        return isset($this->bindings[$request::class]);
    }
}