<?php

namespace Project\Common\Events;

use Psr\EventDispatcher\EventDispatcherInterface;

trait DispatchEventsTrait
{
    protected ?EventDispatcherInterface $dispatcher;

    public function setDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
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