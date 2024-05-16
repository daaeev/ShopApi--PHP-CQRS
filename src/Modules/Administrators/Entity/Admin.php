<?php

namespace Project\Modules\Administrators\Entity;

use Project\Common\Entity\Aggregate;
use Webmozart\Assert\Assert;
use Project\Common\Administrators\Role;
use Project\Modules\Administrators\Api\Events\AdminCreated;
use Project\Modules\Administrators\Api\Events\AdminDeleted;
use Project\Modules\Administrators\Api\Events\AdminRolesChanged;
use Project\Modules\Administrators\Api\Events\AdminLoginChanged;
use Project\Modules\Administrators\Api\Events\AdminPasswordChanged;

class Admin extends Aggregate
{
    private AdminId $id;
    private string $name;
    private string $login;
    private array $roles;

    // Used only for save password!
    // Repository does not retrieve password
    private ?string $password;

    public function __construct(
        AdminId $id,
        string $name,
        string $login,
        string $password,
        array $roles,
    ) {
        Assert::notEmpty($name && $login && $password && $roles);
        Assert::allIsInstanceOf($roles, Role::class);

        $this->id = $id;
        $this->name = $name;
        $this->login = $login;
        $this->password = $password;
        $this->roles = $roles;

        $this->guardCorrectPassword();
        $this->guardCorrectLogin();
        $this->addEvent(new AdminCreated($this));
    }

    private function guardCorrectLogin(): void
    {
        Assert::greaterThan(mb_strlen($this->login), 6, 'Login length must be greater than 6 characters');

        if (str_contains($this->login, ' ')) {
            throw new \DomainException('Login must not contains whitespaces');
        }
    }

    private function guardCorrectPassword(): void
    {
        Assert::greaterThan(mb_strlen($this->password), 6, 'Password length must be greater than 6 characters');

        if (str_contains($this->password, ' ')) {
            throw new \DomainException('Password must not contains whitespaces');
        }
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
    }

    public function setName(string $name): void
    {
        Assert::notEmpty($name);
        $this->name = $name;
    }

    public function setPassword(string $password): void
    {
        Assert::notEmpty($password);

        if ($this->password === $password) {
            return;
        }

        $this->password = $password;
        $this->guardCorrectPassword();
        $this->addEvent(new AdminPasswordChanged($this));
    }

    public function setLogin(string $login): void
    {
        Assert::notEmpty($login);

        if ($this->login === $login) {
            return;
        }

        $this->login = $login;
        $this->guardCorrectLogin();
        $this->addEvent(new AdminLoginChanged($this));
    }

    public function setRoles(array $roles): void
    {
        Assert::notEmpty($roles);
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
