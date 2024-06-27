<?php

namespace Project\Modules\Shopping\Discounts\Promotions\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetPromotionQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
        public readonly array $options = [],
    ) {}
}