<?php

namespace Project\Modules\Administrators\Repository;

use Project\Modules\Administrators\Entity;

interface AdminRepositoryInterface
{
    public function add(Entity\Admin $entity): void;

    public function update(Entity\Admin $entity): void;

    public function delete(Entity\Admin $entity): void;

    public function get(Entity\AdminId $id): Entity\Admin;
}