<?php

namespace Project\Common\CQRS\Buses;

use DomainException;
use Project\Common\CQRS\Handlers;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

class RequestBus implements Interfaces\RequestBus
{
    private array $bindings;
    private ContainerInterface $container;

    public function __construct(array $bindings, ContainerInterface $container)
    {
        Assert::allIsInstanceOf(
            $bindings,
            Handlers\Interfaces\RequestHandler::class,
            'All handlers must be RequestHandler instances'
        );

        $this->bindings = $bindings;
        $this->container = $container;
    }

    public function dispatch($command): mixed
    {
        if (!$this->canDispatch($command)) {
            throw new DomainException('Cant dispatch command');
        }

        return $this->retrieveHandler($command)->handle($command);
    }

    public function canDispatch($command): bool
    {
        return isset($this->bindings[$command]);
    }

    private function retrieveHandler($command): Handlers\Interfaces\RequestHandler
    {
        $this->container->get($this->bindings[$command]);
    }
}