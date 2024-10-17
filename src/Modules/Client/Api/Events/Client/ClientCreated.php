<?php

namespace Project\Modules\Client\Api\Events\Client;

use Project\Modules\Client\Api\Events\ClientEvent;
use Project\Modules\Client\Api\Events\AbstractClientEvent;

class ClientCreated extends AbstractClientEvent
{
    public function getEventId(): string
    {
        return ClientEvent::CREATED->value;
    }
}