<?php

namespace Project\Modules\Administrators\Api\Events;

class AdminRolesChanged extends AbstractAdminEvent
{
    public function getEventId(): string
    {
        return AdministratorsEvent::ROLES_CHANGED->value;
    }
}