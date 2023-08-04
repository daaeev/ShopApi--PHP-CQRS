<?php

namespace Project\Modules\Catalogue\Categories\Queries;

class GetCategoryQuery
{
    public function __construct(
        public readonly int $id
    ) {}
}