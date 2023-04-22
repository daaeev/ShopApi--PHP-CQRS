<?php

namespace Project\Modules\Product\Entity\Size;

enum Size : string
{
    case XS = 'xs';
    case S = 's';
    case M = 'm';
    case L = 'l';
    case XL = 'xl';
    case XLL = 'xll';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
