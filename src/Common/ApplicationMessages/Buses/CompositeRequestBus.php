<?php

namespace Project\Common\ApplicationMessages\Buses;

use Webmozart\Assert\Assert;
use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CompositeRequestBus extends AbstractCompositeMessageBus
{
    public function dispatch(ApplicationMessageInterface $message)
    {
        foreach ($this->buses as $bus) {
            if ($bus->canDispatch($message)) {
                return $bus->dispatch($message);
            }
        }

        throw new \DomainException('Cant dispatch message ' . $message::class);
    }

    public function registerBus(MessageBusInterface $bus): void
    {
        Assert::isInstanceOf($bus, RequestBus::class);
        parent::registerBus($bus);
    }
}