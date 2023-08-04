<?php

namespace Project\Modules\Catalogue\Product\Queries;

class ProductsListQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $params = [],
    ) {}
}