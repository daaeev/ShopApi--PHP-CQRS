<?php

namespace Project\Modules\Catalogue\Content\Product\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class DeleteProductImageCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id
    ) {}
}