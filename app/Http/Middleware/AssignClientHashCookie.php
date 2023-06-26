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
        private int $cookieLifeTimeInMinutes = 1440
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->hashAssigned()) {
            Cookie::queue(
                $this->cookieName,
                Str::random(),
                $this->cookieLifeTimeInMinutes
            );
        }

        return $next($request);
    }

    private function hashAssigned(): bool
    {
        return Cookie::has($this->cookieName);
    }
}
