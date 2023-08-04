<?php

namespace Project\Modules\Catalogue\Categories\Queries;

class CategoriesListQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}