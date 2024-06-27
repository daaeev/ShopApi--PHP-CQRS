<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetPromocodesListQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}