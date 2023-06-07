<?php

namespace Project\Modules\Administrators\AuthManager;

interface AuthManagerInterface
{
    public function login(string $login, string $password): void;

    public function logout(): void;

    public function logged(): bool;
}