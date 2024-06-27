<?php

namespace Project\Common\ApplicationMessages\Buses;

use Webmozart\Assert\Assert;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CompositeEventBus extends AbstractCompositeMessageBus
{
    public function dispatch(ApplicationMessageInterface $message): void
    {
        Assert::isInstanceOf($message, Event::class);
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($message)) {
                $bus->dispatch($message);
            }
        }
    }

    public function registerBus(MessageBusInterface $bus): void
    {
        Assert::isInstanceOf($bus, EventBus::class);
        parent::registerBus($bus);
    }
}