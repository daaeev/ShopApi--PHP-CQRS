<?php

namespace Project\Common\Events;

use Project\Common\ApplicationMessages\Buses\MessageBusInterface;

interface DispatchEventsInterface
{
    public function setDispatcher(MessageBusInterface $eventBus): void;

    public function dispatch(Event $event): void;
}