<?php

namespace Project\Common\ApplicationMessages\Buses;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

abstract class AbstractCompositeMessageBus implements MessageBusInterface
{
    protected array $buses = [];

    public function canDispatch(ApplicationMessageInterface $message): bool
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($message)) {
                return true;
            }
        }

        return false;
    }

    public function registerBus(MessageBusInterface $bus): void
    {
        $this->buses[] = $bus;
    }

    abstract public function dispatch(ApplicationMessageInterface $message);
}