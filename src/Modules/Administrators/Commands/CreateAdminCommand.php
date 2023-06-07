<?php

namespace Project\Modules\Administrators\Commands;

class CreateAdminCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $login,
        public readonly string $password,
        public readonly array $roles,
    ) {}
}