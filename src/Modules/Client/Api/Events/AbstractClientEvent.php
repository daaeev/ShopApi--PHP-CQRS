<?php

namespace Project\Modules\Client\Api\Events;

use Project\Modules\Client\Entity\Client;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Client\Utils\ClientEntity2DTOConverter;

abstract class AbstractClientEvent extends Event
{
    public function __construct(
        private Client $client
    ) {}

    public function getData(): array
    {
        return ClientEntity2DTOConverter::convert($this->client)->toArray();
    }
}