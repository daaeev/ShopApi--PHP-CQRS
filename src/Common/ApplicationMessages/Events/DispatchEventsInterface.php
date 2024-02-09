<?php

namespace Project\Common\ApplicationMessages\Events;

use Project\Common\ApplicationMessages\Buses\MessageBusInterface;

interface DispatchEventsInterface
{
    public function setDispatcher(MessageBusInterface $eventBus): void;

    public function dispatch(Event $event): void;
}