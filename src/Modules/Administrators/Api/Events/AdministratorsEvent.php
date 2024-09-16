<?php

namespace Project\Modules\Administrators\Api\Events;

enum AdministratorsEvent: string
{
    case CREATED = 'administrators.created';
    case DELETED = 'administrators.deleted';
    case LOGIN_CHANGED = 'administrators.loginChanged';
    case PASSWORD_CHANGED = 'administrators.passwordChanged';
    case ROLES_CHANGED = 'administrators.rolesChanged';
}
