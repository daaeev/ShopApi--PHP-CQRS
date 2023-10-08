<?php

namespace Project\Modules\Client\Queries\Handlers;

use Project\Modules\Client\Queries\GetClientQuery;
use Project\Modules\Client\Repository\QueryClientsRepositoryInterface;

class GetClientHandler
{
    public function __construct(
        private QueryClientsRepositoryInterface $clients
    ) {}

    public function __invoke(GetClientQuery $query): array
    {
        return $this->clients->get($query->id)->toArray();
    }
}