<?php

namespace Project\Modules\Client\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetClientsQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}