<?php

namespace Project\Modules\Client\Api\Events\Confirmation;

use Project\Modules\Client\Api\Events\ClientEvent;
use Project\Modules\Client\Api\Events\AbstractClientConfirmationEvent;

class ClientConfirmationCreated extends AbstractClientConfirmationEvent
{
    public function getEventId(): string
    {
        return ClientEvent::CONFIRMATION_CREATED->value;
    }
}