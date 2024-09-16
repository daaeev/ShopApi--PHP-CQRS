<?php

namespace Project\Modules\Client\Api\Events;

enum ClientEvent: string
{
    case CREATED = 'clients.created';
    case UPDATED = 'clients.updated';
}
