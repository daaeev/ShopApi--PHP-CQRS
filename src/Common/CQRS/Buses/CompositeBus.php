<?php

namespace Project\Common\CQRS\Buses;

class CompositeBus extends Interfaces\AbstractCompositeBus
{
    public function dispatch(object $command): mixed
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($command)) {
                return $bus->dispatch($command);
            }
        }

        throw new \DomainException('Cant dispatch command ' . $command::class);
    }
}