<?php

namespace Project\Modules\Administrators\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class CreateAdminCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $login,
        public readonly string $password,
        public readonly array $roles,
    ) {}
}