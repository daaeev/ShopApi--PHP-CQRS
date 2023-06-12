<?php

namespace Project\Modules\Product\Entity\Color;

use DomainException;

class ColorTypeMapper
{
    private static array $types = [
        'hex' => HexColor::class
    ];

    public static function makeByType(string $type, string $name, string $color): Color
    {
        if (!isset(self::$types[$type])) {
            throw new DomainException('Color type does not exists');
        }

        return new self::$types[$type]($name, $color);
    }

    public static function getType(Color $color): string
    {
        if (($type = array_search($color::class, self::$types)) === false) {
            throw new DomainException('Color does not have registered type');
        }

        return $type;
    }

    public static function getTypes(): array
    {
        return array_keys(self::$types);
    }
}