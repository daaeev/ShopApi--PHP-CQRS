<?php

namespace Project\Modules\Catalogue\Categories\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CategoriesListQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}