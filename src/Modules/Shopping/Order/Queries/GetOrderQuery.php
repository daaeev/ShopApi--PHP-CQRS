<?php

namespace Project\Modules\Shopping\Order\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetOrderQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int|string $id,
        public readonly bool $filterByClient = false,
    ) {}
}