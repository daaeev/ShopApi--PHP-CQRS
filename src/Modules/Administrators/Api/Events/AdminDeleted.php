<?php

namespace Project\Modules\Administrators\Api\Events;

class AdminDeleted extends AbstractAdminEvent
{
    public function getEventId(): string
    {
        return AdministratorsEvent::DELETED->value;
    }
}