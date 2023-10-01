<?php

namespace Project\Tests\Unit\Modules\Helpers;

use Project\Common\Administrators\Role;
use Project\Modules\Administrators\Entity\Admin;
use Project\Modules\Administrators\Entity\AdminId;

trait AdminFactory
{
    private string $correctAdminPassword = 'AdminPassword';
    private string $correctAdminLogin = 'AdminLogin';

    private function generateAdmin(): Admin
    {
        $admin = new Admin(
            AdminId::next(),
            md5(rand()),
            md5(rand()),
            md5(rand()),
            [Role::ADMIN],
        );
        $admin->flushEvents();
        return $admin;
    }

    private function makeAdmin(
        AdminId $id,
        string $name,
        string $login,
        string $password,
        array $roles,
    ): Admin {
        return new Admin(
            $id,
            $name,
            $login,
            $password,
            $roles,
        );
    }
}