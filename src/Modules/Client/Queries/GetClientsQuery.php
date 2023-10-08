<?php

namespace Project\Modules\Client\Queries;

class GetClientsQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}