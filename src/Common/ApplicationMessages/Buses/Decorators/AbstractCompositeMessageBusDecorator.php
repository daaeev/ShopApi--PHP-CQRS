<?php

namespace Project\Common\ApplicationMessages\Buses\Decorators;

use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Common\ApplicationMessages\Buses\AbstractCompositeMessageBus;

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