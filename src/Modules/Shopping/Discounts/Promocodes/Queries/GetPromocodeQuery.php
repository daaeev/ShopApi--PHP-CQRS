<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetPromocodeQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
        public readonly array $options = [],
    ) {}
}