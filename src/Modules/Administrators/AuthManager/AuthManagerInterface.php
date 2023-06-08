<?php

namespace Project\Modules\Administrators\AuthManager;

use Project\Modules\Administrators\Entity;

interface AuthManagerInterface
{
    public function login(string $login, string $password): void;

    public function logout(): void;

    public function logged(): ?Entity\Admin;
}