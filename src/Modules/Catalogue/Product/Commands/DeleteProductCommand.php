<?php

namespace Project\Modules\Catalogue\Product\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class DeleteProductCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
    ) {}
}