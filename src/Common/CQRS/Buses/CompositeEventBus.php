<?php

namespace Project\Common\CQRS\Buses;

class CompositeEventBus extends AbstractCompositeMessageBus
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