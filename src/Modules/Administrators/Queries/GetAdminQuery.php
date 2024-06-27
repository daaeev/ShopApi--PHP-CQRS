<?php

namespace Project\Modules\Administrators\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetAdminQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id
    ) {}
}