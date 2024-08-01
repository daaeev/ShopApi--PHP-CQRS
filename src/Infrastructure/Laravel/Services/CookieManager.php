<?php

namespace Project\Infrastructure\Laravel\Services;

use Illuminate\Support\Facades\Cookie;
use Project\Common\Services\Cookie\CookieManagerInterface;

class CookieManager implements CookieManagerInterface
{
    public function add(string $key, int|string $value, int $lifeTimeInMinutes = 1440): void
    {
        Cookie::queue($key, $value, $lifeTimeInMinutes);
    }

    public function get(string $key): int|string|null
    {
        $queued = Cookie::queued($key);
        return $queued?->getValue() ?? Cookie::get($key);
    }
}