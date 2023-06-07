<?php

namespace Project\Modules\Administrators\Queries;

class AdminsListQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}