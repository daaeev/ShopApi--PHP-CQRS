<?php

namespace Project\Modules\Catalogue\Categories\Queries;

class GetCategoriesQuery
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}