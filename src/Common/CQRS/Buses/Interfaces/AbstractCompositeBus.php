<?php

namespace Project\Common\CQRS\Buses\Interfaces;

abstract class AbstractCompositeBus implements RequestBus
{
    protected array $buses = [];

    public function canDispatch($command): bool
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($command)) {
                return true;
            }
        }

        return false;
    }

    public function registerBus(RequestBus $bus): void
    {
        $this->buses[] = $bus;
    }

    abstract public function dispatch(object $command);
}