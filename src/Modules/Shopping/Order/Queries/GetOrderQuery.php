<?php

namespace Project\Modules\Shopping\Order\Queries;

class GetOrderQuery
{
    public function __construct(
        public readonly int|string $id,
        public readonly bool $filterByClient = false,
    ) {}
}