<?php

namespace Project\Common\CQRS\Buses;

abstract class AbstractCompositeMessageBus implements MessageBusInterface
{
    protected array $buses = [];

    public function canDispatch(object $request): bool
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($request)) {
                return true;
            }
        }

        return false;
    }

    public function registerBus(MessageBusInterface $bus): void
    {
        $this->buses[] = $bus;
    }

    abstract public function dispatch(object $request);
}