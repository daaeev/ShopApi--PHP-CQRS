<?php

namespace Project\Modules\Product\Entity;

enum Availability : string
{
    case IN_STOCK = 'in_stock';
    case PREORDER = 'preorder';
    case OUT_STOCK = 'out_stock';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
