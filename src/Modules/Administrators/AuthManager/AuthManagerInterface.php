<?php

namespace Project\Modules\Administrators\AuthManager;

use Project\Modules\Administrators\Entity;

interface AuthManagerInterface
{
    public function login(Entity\Admin $admin): void;

    public function logout(): void;

    public function logged(): bool;
}