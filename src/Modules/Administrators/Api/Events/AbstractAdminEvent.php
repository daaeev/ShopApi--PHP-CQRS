<?php

namespace Project\Modules\Administrators\Api\Events;

use Project\Modules\Administrators\Entity;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Administrators\Utils\AdministratorEntity2DTOConverter;

abstract class AbstractAdminEvent extends Event
{
    public function __construct(
        private readonly Entity\Admin $admin
    ) {}

    public function getData(): array
    {
        return AdministratorEntity2DTOConverter::convert($this->admin)->toArray();
    }
}