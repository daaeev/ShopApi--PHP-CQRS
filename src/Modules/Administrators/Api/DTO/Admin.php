<?php

namespace Project\Modules\Administrators\Api\DTO;

use Webmozart\Assert\Assert;
use Project\Common\Utils\DTO;
use Project\Common\Administrators\Role;

class Admin implements DTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $login,
        public readonly array $roles,
    ) {
        Assert::allIsInstanceOf($this->roles, Role::class);
    }

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