<?php

namespace Project\Modules\Catalogue\Product\Queries;

class GetProductQuery
{
    public function __construct(
        public readonly int $id
    ) {}
}