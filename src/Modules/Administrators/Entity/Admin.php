<?php

namespace Project\Modules\Administrators\Entity;

use Project\Common\Events;
use Webmozart\Assert\Assert;
use Project\Common\Administrators\Role;
use Project\Modules\Administrators\Api\Events\AdminLoginChanged;
use Project\Modules\Administrators\Api\Events\AdminRolesChanged;
use Project\Modules\Administrators\Api\Events\AdminDeleted;
use Project\Modules\Administrators\Api\Events\AdminPasswordChanged;

class Admin implements Events\EventRoot
{
    use Events\EventTrait;

    // Used only for save password!
    // Repository does not retrieve password
    private ?string $password;

    public function __construct(
        private AdminId $id,
        private string $name,
        private string $login,
        string $password,
        private array $roles,
    ) {
        Assert::notEmpty($name && $login && $password && $roles);
        Assert::allIsInstanceOf($roles, Role::class);
        $this->password = $password;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPassword(string $password): void
    {
        if ($this->password === $password) {
            return;
        }

        $this->password = $password;
        $this->addEvent(new AdminPasswordChanged($this));
    }

    public function setLogin(string $login): void
    {
        if ($this->login === $login) {
            return;
        }

        $this->login = $login;
        $this->addEvent(new AdminLoginChanged($this));
    }

    public function setRoles(array $roles): void
    {
        Assert::allIsInstanceOf($roles, Role::class);
        if ($this->sameRoles($roles)) {
            return;
        }

        $this->roles = $roles;
        $this->addEvent(new AdminRolesChanged($this));
    }

    private function sameRoles(array $roles): bool
    {
        if (count($roles) !== count($this->roles)) {
            return false;
        }

        foreach ($roles as $role) {
            if (!in_array($role, $this->roles)) {
                return false;
            }
        }

        return true;
    }

    public function delete(): void
    {
        $this->addEvent(new AdminDeleted($this));
    }

    public function getId(): AdminId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}