<?php

namespace Project\Common;

enum Language : string
{
    case UA = 'ua';
    case EN = 'en';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function default(): self
    {
        return self::UA;
    }

    public static function active(): array
    {
        return array_filter(self::cases(), function (self $language) {
            return $language->isActive();
        });
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::UA,
            self::EN,
        ]);
    }
}
