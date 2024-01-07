<?php

namespace Project\Common\Events;

use Project\Common\CQRS\Buses\MessageBusInterface;

interface DispatchEventsInterface
{
    public function setDispatcher(MessageBusInterface $eventBus): void;

    public function dispatch(Event $event): void;
}