<?php

namespace Project\Modules\Administrators\Entity;

use Project\Common\Events;
use Webmozart\Assert\Assert;
use Project\Common\Administrators\Role;

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