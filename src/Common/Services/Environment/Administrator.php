<?php

namespace Project\Common\Services\Environment;

use Webmozart\Assert\Assert;
use Project\Common\Administrators\Role;

class Administrator
{
    public function __construct(
        private int $id,
        private string $name,
        private array $roles = [],
    ) {
        Assert::notEmpty($id);
        Assert::allIsInstanceOf($roles, Role::class);
    }

    public function hasAccess(Role $role): bool
    {
        return match ($role) {
            Role::ADMIN => $this->hasRole($role),
            Role::MANAGER => $this->hasRole($role) || $this->hasRole(Role::ADMIN),
            default => false
        };
    }

    private function hasRole(Role $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function same(self $other): bool
    {
        return $this->id === $other->id;
    }
}