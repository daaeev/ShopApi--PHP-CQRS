<?php

namespace Project\Modules\Catalogue\Product\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetProductQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id
    ) {}
}