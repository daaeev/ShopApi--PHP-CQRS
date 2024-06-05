<?php

namespace Project\Modules\Shopping\Order\Queries;

class GetOrdersQuery
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 15,
        public readonly array $filters = [],
        public readonly array $sorting = [],
    ) {}
}