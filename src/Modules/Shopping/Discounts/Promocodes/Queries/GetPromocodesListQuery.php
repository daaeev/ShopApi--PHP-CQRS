<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Queries;

class GetPromocodesListQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}