<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel\AuthManager;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Project\Common\Administrators\Role;
use Project\Modules\Administrators\Entity;
use Project\Common\Entity\Hydrator\Hydrator;
use Illuminate\Auth\AuthenticationException;
use Project\Modules\Administrators\AuthManager\AuthManagerInterface;

class GuardAuthManager implements AuthManagerInterface
{
    private Guard $guard;

    public function __construct(
        private Hydrator $hydrator
    ) {
        $this->guard = Auth::guard('admin');
    }

    public function login(string $login, string $password): void
    {
        if ($this->guard->check()) {
            throw new AuthenticationException('You already authorized');
        }

        $this->guard->attempt([
            'login' => $login,
            'password' => $password
        ], remember: true);

        if (!$this->guard->check()) {
            throw new AuthenticationException('Credentials does not match');
        }
    }

    public function logout(): void
    {
        if (!$this->guard->check()) {
            throw new AuthenticationException('You does not authorized');
        }

        $this->guard->logout();
        Request::session()->invalidate();
        Request::session()->regenerateToken();
    }

    public function logged(): ?Entity\Admin
    {
        if (!$this->guard->check()) {
            return null;
        }

        $admin = $this->guard->user();
        return $this->hydrator->hydrate(Entity\Admin::class, [
            'id' => new Entity\AdminId($admin->id),
            'name' => $admin->name,
            'login' => $admin->login,
            'roles' => array_map(function (string $role) {
                return Role::from($role);
            }, $admin->roles),
        ]);
    }
}