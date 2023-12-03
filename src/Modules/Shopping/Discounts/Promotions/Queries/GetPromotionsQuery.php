<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Queries;

class GetPromotionsQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}