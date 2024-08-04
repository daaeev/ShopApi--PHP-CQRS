<?php

namespace Project\Infrastructure\Laravel\Middleware;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Project\Common\Services\Cookie\CookieManagerInterface;

class AssignClientHashCookie
{
    public function __construct(
        private CookieManagerInterface $cookie,
        private string $cookieName = 'clientHash',
        private int $cookieLifeTimeInMinutes = 1440,
        private int $hashLength = 40
    ) {}

    public function handle(Request $request, \Closure $next): Response
    {
        if (!$this->hashAssigned() || !$this->hashIsValid()) {
            $this->cookie->add($this->cookieName, $this->generateHash(), $this->cookieLifeTimeInMinutes);
        }

        return $next($request);
    }

    private function hashAssigned(): bool
    {
        return !empty($this->cookie->get($this->cookieName));
    }

    private function hashIsValid(): bool
    {
        $hash = $this->cookie->get($this->cookieName);
        return is_string($hash) && (mb_strlen($hash) === $this->hashLength);
    }

    private function generateHash(): string
    {
        return Str::random($this->hashLength);
    }
}
