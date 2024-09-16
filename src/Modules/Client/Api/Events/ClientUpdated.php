<?php

namespace Project\Modules\Client\Api\Events;

class ClientUpdated extends AbstractClientEvent
{
    public function getEventId(): string
    {
        return ClientEvent::UPDATED->value;
    }
}