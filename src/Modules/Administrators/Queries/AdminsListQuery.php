<?php

namespace Project\Modules\Administrators\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class AdminsListQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $page,
        public readonly int $limit,
        public readonly array $options = [],
    ) {}
}