<?php

namespace Project\Modules\Administrators\Api\DTO;

use Project\Common\Utils\DTO;

class Admin implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $login,
        public readonly array $roles,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'login' => $this->login,
            'roles' => $this->roles,
        ];
    }
}