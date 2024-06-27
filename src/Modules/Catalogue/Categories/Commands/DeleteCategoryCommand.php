<?php

namespace Project\Modules\Catalogue\Categories\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class DeleteCategoryCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
    ) {}
}