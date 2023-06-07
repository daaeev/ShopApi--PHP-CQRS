<?php

namespace Project\Modules\Administrators\Api\Events;

use Project\Common\Utils;
use Project\Common\Events\Event;
use Project\Modules\Administrators\Entity;
use Project\Modules\Administrators\Utils\Entity2DTOConverter;

class AbstractAdminEvent extends Event
{
    public function __construct(
        private Entity\Admin $admin
    ) {}

    public function getDTO(): Utils\DTO
    {
        return Entity2DTOConverter::convert($this->admin);
    }
}