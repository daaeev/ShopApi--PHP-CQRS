<?php

namespace Project\Modules\Client\Api\Events;

class ClientCreated extends AbstractClientEvent
{
    public function getEventId(): string
    {
        return ClientEvent::CREATED->value;
    }
}