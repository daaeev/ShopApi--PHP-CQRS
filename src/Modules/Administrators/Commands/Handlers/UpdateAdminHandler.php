<?php

namespace Project\Modules\Administrators\Commands\Handlers;

use Project\Common\Administrators\Role;
use Project\Common\Events\DispatchEventsTrait;
use Project\Modules\Administrators\Entity\Admin;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Modules\Administrators\Commands\UpdateAdminCommand;
use Project\Modules\Administrators\Repository\AdminRepositoryInterface;

class UpdateAdminHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private AdminRepositoryInterface $admins
    ) {}

    public function __invoke(UpdateAdminCommand $command): void
    {
        $entity = $this->admins->get(new AdminId($command->id));
        $entity->setName($command->name);
        $entity->setLogin($command->login);
        $entity->setRoles(array_map(function (string $role) {
            return Role::from($role);
        }, $command->roles));
        if (!empty($command->password)) {
            $entity->setPassword($command->password);
        }

        $this->admins->update($entity);
        $this->dispatchEvents($entity->flushEvents());
    }
}