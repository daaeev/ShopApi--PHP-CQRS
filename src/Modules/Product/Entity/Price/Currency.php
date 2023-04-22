<?php

namespace Project\Modules\Product\Entity\Price;

enum Currency : string
{
    case UAH = 'uah';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function default(): self
    {
        return self::UAH;
    }

    public static function active(): array
    {
        return [
            self::UAH
        ];
    }
}
