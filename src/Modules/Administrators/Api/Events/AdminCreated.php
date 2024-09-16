<?php

namespace Project\Modules\Administrators\Api\Events;

class AdminCreated extends AbstractAdminEvent
{
    public function getEventId(): string
    {
        return AdministratorsEvent::CREATED->value;
    }
}