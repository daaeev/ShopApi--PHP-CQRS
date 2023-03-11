<?php

namespace Project\Common\CQRS\Buses;

use DomainException;
use Project\Common\CQRS\Buses\Interfaces\RequestBus;

class ChainBus implements Interfaces\ChainBus
{
    private array $buses = [];

    public function dispatch(object $command): mixed
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($command)) {
                return $bus->dispatch($command);
            }
        }

        throw new DomainException('Cant dispatch command ' . $command::class);
    }

    public function registerBus(RequestBus $bus): void
    {
        $this->buses[] = $bus;
    }
}