<?php

namespace Project\Common\CQRS\Buses;

use Psr\EventDispatcher\EventDispatcherInterface;

class CompositeEventBus extends Interfaces\AbstractCompositeBus implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($event)) {
                $bus->dispatch($event);
            }
        }
    }
}