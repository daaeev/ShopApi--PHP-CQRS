<?php

namespace Project\Common\ApplicationMessages\Buses\Decorators;

use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;
use Project\Common\ApplicationMessages\Buses\AbstractCompositeMessageBus;

abstract class AbstractCompositeMessageBusDecorator extends AbstractCompositeMessageBus
{
    public function __construct(
        protected AbstractCompositeMessageBus $decorated
    ) {}

    public function dispatch(ApplicationMessageInterface $message)
    {
        return $this->decorated->dispatch($message);
    }

    public function canDispatch(ApplicationMessageInterface $message): bool
    {
        return $this->decorated->canDispatch($message);
    }

    public function registerBus(MessageBusInterface $bus): void
    {
        $this->decorated->registerBus($bus);
    }
}