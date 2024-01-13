<?php

namespace Project\Common\Events;

use Project\Common\ApplicationMessages\Buses\MessageBusInterface;

trait DispatchEventsTrait
{
    protected ?MessageBusInterface $dispatcher;

    public function setDispatcher(MessageBusInterface $eventBus): void
    {
        $this->dispatcher = $eventBus;
    }

    public function dispatch(Event $event): void
    {
        $this->checkDispatcherInstantiate();
        $this->dispatcher->dispatch($event);
    }

    private function checkDispatcherInstantiate(): void
    {
        if (!isset($this->dispatcher)) {
            throw new \DomainException('Dispatcher has not been instantiate');
        }
    }

    public function dispatchEvents(array $events): void
    {
        $this->checkDispatcherInstantiate();
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}