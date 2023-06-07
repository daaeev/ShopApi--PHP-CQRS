<?php

namespace Project\Modules\Administrators\Commands;

class AuthorizeCommand
{
    public function __construct(
        public readonly string $login,
        public readonly string $password,
    ) {}
}