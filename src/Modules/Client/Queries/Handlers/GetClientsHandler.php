<?php

namespace Project\Modules\Client\Queries\Handlers;

use Project\Modules\Client\Queries\GetClientsQuery;
use Project\Modules\Client\Repository\QueryClientsRepositoryInterface;

class GetClientsHandler
{
    public function __construct(
        private QueryClientsRepositoryInterface $clients
    ) {}

    public function __invoke(GetClientsQuery $query): array
    {
        return $this->clients->list(
            $query->page,
            $query->limit,
            $query->options,
        )->toArray();
    }
}