<?php

namespace Project\Common\Events;

trait EventTrait
{
    protected array $events = [];

    public function flushEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    public function addEvent(Event $event): void
    {
        if (!in_array($event, $this->events)) {
            $this->events[] = $event;
        }
    }
}