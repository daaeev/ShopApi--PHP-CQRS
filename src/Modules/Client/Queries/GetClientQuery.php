<?php

namespace Project\Modules\Client\Queries;

class GetClientQuery
{
    public function __construct(
        public readonly int $id
    ) {}
}