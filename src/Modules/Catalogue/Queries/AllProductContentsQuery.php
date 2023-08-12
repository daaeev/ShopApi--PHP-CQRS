<?php

namespace Project\Modules\Catalogue\Queries;

class AllProductContentsQuery
{
    public function __construct(
        public readonly int $id,
        public readonly array $options = [],
    ) {}
}