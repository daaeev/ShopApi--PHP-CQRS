<?php

namespace Project\Common\CQRS\Buses;

use DomainException;
use Project\Common\CQRS\Buses\Interfaces\RequestBus;

class ChainBus implements Interfaces\ChainBus
{
    private array $buses = [];

    public function dispatch($command): mixed
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($command)) {
                return $bus->dispatch($command);
            }
        }
    }

    public function registerBus(RequestBus $bus): void
    {
        if (in_array($bus, $this->buses)) {
            throw new DomainException('Bus already registered');
        }

        $this->buses[] = $bus;
    }
}