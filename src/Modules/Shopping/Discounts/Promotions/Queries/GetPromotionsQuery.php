<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetPromotionsQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}