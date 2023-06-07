<?php

namespace Project\Modules\Administrators\Commands\Handlers;

use Project\Common\Events\DispatchEventsTrait;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Modules\Administrators\Commands\DeleteAdminCommand;
use Project\Modules\Administrators\Repository\AdminRepositoryInterface;

class DeleteAdminHandler implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    public function __construct(
        private AdminRepositoryInterface $admins
    ) {}

    public function __invoke(DeleteAdminCommand $command): void
    {
        $entity = $this->admins->get(new AdminId($command->id));
        $entity->delete();
        $this->admins->delete($entity);
        $this->dispatchEvents($entity->flushEvents());
    }
}