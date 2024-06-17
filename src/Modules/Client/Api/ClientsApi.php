<?php

namespace Project\Modules\Client\Api;

use Project\Modules\Client\Api\DTO;
use Project\Common\ApplicationMessages\Events\DispatchEventsTrait;
use Project\Common\ApplicationMessages\Events\DispatchEventsInterface;
use Project\Modules\Client\Repository\QueryClientsRepositoryInterface;

class ClientsApi implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private QueryClientsRepositoryInterface $clients,
    ) {}

    public function get(int|string $id): DTO\Client
    {
        return $this->clients->get($id);
    }
}