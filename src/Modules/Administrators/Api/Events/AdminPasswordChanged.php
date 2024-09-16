<?php

namespace Project\Modules\Administrators\Api\Events;

class AdminPasswordChanged extends AbstractAdminEvent
{
    public function getEventId(): string
    {
        return AdministratorsEvent::PASSWORD_CHANGED->value;
    }
}