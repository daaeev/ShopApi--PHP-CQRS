<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AssignClientHashCookie
{
    public function __construct(
        private string $cookieName = 'clientHash',
        private int $cookieLifeTimeInMinutes = 1440,
        private int $hashLegth = 40
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->hashAssigned() || !$this->hashIsValid()) {
            Cookie::queue(
                $this->cookieName,
                $this->generateHash(),
                $this->cookieLifeTimeInMinutes
            );
        }

        return $next($request);
    }

    private function hashAssigned(): bool
    {
        return Cookie::has($this->cookieName);
    }

    private function hashIsValid(): bool
    {
        $hash = Cookie::get($this->cookieName);

        return (
            is_string($hash)
            && (mb_strlen($hash) === $this->hashLegth)
        );
    }

    private function generateHash(): string
    {
        return Str::random($this->hashLegth);
    }
}
