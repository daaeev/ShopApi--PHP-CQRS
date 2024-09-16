<?php

namespace Project\Modules\Administrators\Api\Events;

class AdminLoginChanged extends AbstractAdminEvent
{
    public function getEventId(): string
    {
        return AdministratorsEvent::LOGIN_CHANGED->value;
    }
}