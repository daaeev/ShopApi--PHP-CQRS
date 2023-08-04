<?php

namespace Project\Modules\Catalogue\Queries;

class ProductDetailsQuery
{
    public function __construct(
        public readonly string $code,
        public readonly array $options = [],
    ) {}
}