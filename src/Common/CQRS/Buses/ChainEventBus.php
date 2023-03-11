<?php

namespace Project\Common\CQRS\Buses;

use Project\Common\CQRS\Buses\Interfaces\RequestBus;

class ChainEventBus implements Interfaces\ChainBus
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