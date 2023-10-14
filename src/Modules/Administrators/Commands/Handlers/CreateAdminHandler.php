<?php

namespace Project\Modules\Administrators\Commands\Handlers;

use Project\Common\Administrators\Role;
use Project\Common\Events\DispatchEventsTrait;
use Project\Modules\Administrators\Entity\Admin;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Modules\Administrators\Commands\CreateAdminCommand;
use Project\Modules\Administrators\Repository\AdminsRepositoryInterface;

class CreateAdminHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private AdminsRepositoryInterface $admins
    ) {}

    public function __invoke(CreateAdminCommand $command): int
    {
        $entity = new Admin(
            AdminId::next(),
            $command->name,
            $command->login,
            $command->password,
            array_map(function (string $role) {
                return Role::from($role);
            }, $command->roles)
        );

        $this->admins->add($entity);
        $this->dispatchEvents($entity->flushEvents());
        return $entity->getId()->getId();
    }
}