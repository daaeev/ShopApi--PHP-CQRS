<?php

namespace Project\Common\ApplicationMessages\Buses;

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