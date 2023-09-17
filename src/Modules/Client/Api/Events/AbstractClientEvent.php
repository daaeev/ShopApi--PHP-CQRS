<?php

namespace Project\Modules\Client\Api\Events;

use Project\Common\Utils;
use Project\Common\Events\Event;
use Project\Modules\Client\Entity\Client;
use Project\Modules\Client\Utils\ClientEntity2DTOConverter;

abstract class AbstractClientEvent extends Event
{
    public function __construct(
        private Client $client
    ) {}

    public function getDTO(): Utils\DTO
    {
        return ClientEntity2DTOConverter::convert($this->client);
    }
}