<?php

namespace Project\Modules\Client\Queries;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class GetClientQuery implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id
    ) {}
}