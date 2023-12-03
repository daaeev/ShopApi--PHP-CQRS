<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Queries;

class GetPromotionQuery
{
    public function __construct(
        public readonly int $id,
        public readonly array $options = [],
    ) {}
}