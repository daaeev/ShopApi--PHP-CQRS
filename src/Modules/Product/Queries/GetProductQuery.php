<?php

namespace Project\Modules\Product\Queries;

class GetProductQuery
{
    public function __construct(
        public readonly int $id
    ) {}
}