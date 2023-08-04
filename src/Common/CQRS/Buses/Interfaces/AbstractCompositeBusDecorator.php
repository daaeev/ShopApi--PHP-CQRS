<?php

namespace Project\Common\CQRS\Buses\Interfaces;

class AbstractCompositeBusDecorator extends AbstractCompositeBus
{
    public function __construct(
        protected AbstractCompositeBus $decorated
    ) {}

    public function canDispatch(object $command): bool
    {
        return $this->decorated->canDispatch($command);
    }

    public function registerBus(BusInterface $bus): void
    {
        $this->decorated->registerBus($bus);
    }

    public function dispatch(object $command)
    {
        return $this->decorated->dispatch($command);
    }
}