<?php

namespace Project\Common\CQRS\Buses\Interfaces;

abstract class AbstractCompositeBus implements BusInterface
{
    protected array $buses = [];

    public function canDispatch(object $command): bool
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($command)) {
                return true;
            }
        }

        return false;
    }

    public function registerBus(BusInterface $bus): void
    {
        $this->buses[] = $bus;
    }

    public function dispatch(object $command)
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($command)) {
                return $bus->dispatch($command);
            }
        }

        throw new \DomainException('Cant dispatch command ' . $command::class);
    }
}