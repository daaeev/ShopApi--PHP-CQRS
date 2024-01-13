<?php

namespace Project\Common\ApplicationMessages\Buses;

class CompositeRequestBus extends AbstractCompositeMessageBus
{
    public function dispatch(object $request)
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($request)) {
                return $bus->dispatch($request);
            }
        }

        throw new \DomainException('Cant dispatch command ' . $request::class);
    }
}