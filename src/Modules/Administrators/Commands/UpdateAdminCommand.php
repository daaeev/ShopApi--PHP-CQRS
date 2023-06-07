<?php

namespace Project\Modules\Administrators\Commands;

class UpdateAdminCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $login,
        public readonly string $password,
        public readonly array $roles,
    ) {}
}