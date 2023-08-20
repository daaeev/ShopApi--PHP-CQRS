<?php

namespace Project\Modules\Shopping\Discounts\Promocodes\Queries;

class GetPromocodeQuery
{
    public function __construct(
        public readonly int $id,
        public readonly array $options = [],
    ) {}
}