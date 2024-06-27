<?php

namespace Project\Modules\Catalogue\Categories\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetCategoryQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id
    ) {}
}