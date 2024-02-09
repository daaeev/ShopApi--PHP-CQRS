<?php

namespace Project\Modules\Administrators\Api\Events;

use Project\Common\Utils;
use Project\Modules\Administrators\Entity;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Administrators\Utils\AdministratorEntity2DTOConverter;

class AbstractAdminEvent extends Event
{
    public function __construct(
        private Entity\Admin $admin
    ) {}

    public function getDTO(): Utils\DTO
    {
        return AdministratorEntity2DTOConverter::convert($this->admin);
    }
}