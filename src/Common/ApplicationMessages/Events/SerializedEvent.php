<?php

namespace Project\Common\ApplicationMessages\Events;

use Webmozart\Assert\Assert;

class SerializedEvent
{
    private readonly string $eventId;
    private readonly array $data;

    public function __construct(Event $event)
    {
        $this->eventId = $event->getEventId();
        $this->data = $event->getData();
    }

    public function __get(string $name)
    {
        Assert::keyExists($this->data, $name);
        return $this->data[$name];
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }
}