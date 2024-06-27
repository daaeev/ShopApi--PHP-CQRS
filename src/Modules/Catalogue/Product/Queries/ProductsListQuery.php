<?php

namespace Project\Modules\Catalogue\Product\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class ProductsListQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $params = [],
    ) {}
}