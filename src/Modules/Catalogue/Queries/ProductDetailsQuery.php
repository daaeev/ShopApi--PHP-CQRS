<?php

namespace Project\Modules\Catalogue\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class ProductDetailsQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $code,
        public readonly array $options = [],
    ) {}
}