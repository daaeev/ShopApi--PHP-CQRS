<?php

namespace Project\Modules\Administrators\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class DeleteAdminCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly int $id,
    ) {}
}