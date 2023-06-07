<?php

namespace Project\Modules\Administrators\Repository;

use Project\Modules\Administrators\Entity;

interface AdminRepositoryInterface
{
    public function add(Entity\Admin $admin): void;

    public function update(Entity\Admin $admin): void;

    public function delete(Entity\Admin $admin): void;

    public function get(Entity\AdminId $id): void;
}