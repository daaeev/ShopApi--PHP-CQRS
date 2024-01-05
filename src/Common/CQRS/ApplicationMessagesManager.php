<?php

namespace Project\Common\CQRS;

use Project\Common\CQRS\Buses\MessageBusInterface;

class ApplicationMessagesManager
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $queryBus,
        private MessageBusInterface $eventBus,
    ) {}

    public function dispatchCommand(object $command): mixed
    {
        return $this->commandBus->dispatch($command);
    }

    public function dispatchQuery(object $query): mixed
    {
        return $this->queryBus->dispatch($query);
    }

    public function dispatchEvent(object $query): void
    {
        $this->eventBus->dispatch($query);
    }
}