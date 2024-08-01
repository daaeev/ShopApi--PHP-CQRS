<?php

namespace Project\Common\Services\Cookie;

interface CookieManagerInterface
{
    public function add(string $key, int|string $value, int $lifeTimeInMinutes = 1440): void;

    public function get(string $key): int|string|null;
}