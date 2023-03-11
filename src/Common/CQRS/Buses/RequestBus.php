<?php

namespace Project\Common\CQRS\Buses;

use DomainException;
use Psr\Container\ContainerInterface;

class RequestBus implements Interfaces\RequestBus
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
            throw new DomainException('Cant dispatch command ' . $command::class);
        }

        $handler = $this->retrieveHandler($command);

        if (is_callable($handler)) {
            return call_user_func($handler, $command);
        }

        throw new DomainException('Cant execute' . $handler::class . 'request handler');
    }

    public function canDispatch($command): bool
    {
        return isset($this->bindings[$command::class]);
    }

    private function retrieveHandler($command): mixed
    {
        return $this->container->get($this->bindings[$command::class]);
    }
}