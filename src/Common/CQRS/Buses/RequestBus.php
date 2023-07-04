<?php

namespace Project\Common\CQRS\Buses;

use Psr\Container\ContainerInterface;

class RequestBus implements Interfaces\BusInterface
{
    private array $bindings;
    private ContainerInterface $container;

    public function __construct(array $bindings, ContainerInterface $container)
    {
        $this->bindings = $bindings;
        $this->container = $container;
    }

    public function dispatch(object $command): mixed
    {
        if (!$this->canDispatch($command)) {
            throw new \DomainException('Cant dispatch command ' . $command::class);
        }

        $handler = $this->bindings[$command::class];

        if (is_string($handler) && class_exists($handler)) {
            return $this->executeCallableHandler($command, $handler);
        }

        if (is_array($handler) && (count($handler) === 2)) {
            return $this->executeHandlerMethod($command, $handler[0], $handler[1]);
        }

        throw new \DomainException('Cant execute ' . $command::class . ' command handler');
    }

    public function canDispatch(object $command): bool
    {
        return isset($this->bindings[$command::class]);
    }

    private function executeCallableHandler(object $command, string $handlerClass): mixed
    {
        $handler = $this->container->get($handlerClass);

        if (!is_callable($handler)) {
            throw new \DomainException($command::class . ' handler dont callable');
        }

        return call_user_func($handler, $command);
    }

    private function executeHandlerMethod(
        object $command,
        string $handlerClass,
        string $method
    ): mixed {
        $handler = $this->container->get($handlerClass);

        if (!method_exists($handler, $method)) {
            throw new \DomainException($command::class . ' handler does not has ' . $method . ' method');
        }

        return $handler->$method($command);
    }
}