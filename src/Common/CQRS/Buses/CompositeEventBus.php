<?php

namespace Project\Common\CQRS\Buses;

use Psr\EventDispatcher\EventDispatcherInterface;
use Project\Common\CQRS\Buses\Interfaces\RequestBus;

class CompositeEventBus implements Interfaces\ChainBus, EventDispatcherInterface
{
    private array $buses = [];

    public function dispatch(object $command): void
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($command)) {
                $bus->dispatch($command);
            }
        }
    }

    public function registerBus(RequestBus $bus): void
    {
        $this->buses[] = $bus;
    }
}