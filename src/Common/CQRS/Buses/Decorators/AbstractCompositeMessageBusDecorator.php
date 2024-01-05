<?php

namespace Project\Common\CQRS\Buses\Decorators;

use Project\Common\CQRS\Buses\MessageBusInterface;
use Project\Common\CQRS\Buses\AbstractCompositeMessageBus;

abstract class AbstractCompositeMessageBusDecorator extends AbstractCompositeMessageBus
{
    public function __construct(
        protected AbstractCompositeMessageBus $decorated
    ) {}

    public function dispatch(object $request)
    {
        return $this->decorated->dispatch($request);
    }

    public function canDispatch(object $request): bool
    {
        return $this->decorated->canDispatch($request);
    }

    public function registerBus(MessageBusInterface $bus): void
    {
        $this->decorated->registerBus($bus);
    }
}