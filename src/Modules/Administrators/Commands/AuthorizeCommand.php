<?php

namespace Project\Modules\Administrators\Commands;

use Project\Common\ApplicationMessages\ApplicationMessageInterface;

class AuthorizeCommand implements ApplicationMessageInterface
{
    public function __construct(
        public readonly string $login,
        public readonly string $password,
    ) {}
}