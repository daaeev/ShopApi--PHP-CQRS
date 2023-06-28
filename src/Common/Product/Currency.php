<?php

namespace Project\Common\Product;

enum Currency: string
{
    case UAH = 'uah';
    case USD = 'usd';
    case INACTIVE = 'inactive'; // for tests

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
        return array_filter(self::cases(), function (self $currency) {
            return $currency->isActive();
        });
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::UAH,
            self::USD,
        ]);
    }
}
