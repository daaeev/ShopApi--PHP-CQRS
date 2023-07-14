<?php

namespace Project\Modules\Catalogue\Queries;

class ProductsListQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}