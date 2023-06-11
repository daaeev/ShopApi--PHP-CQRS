<?php

namespace Project\Common\Administrators;

enum Role : string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
