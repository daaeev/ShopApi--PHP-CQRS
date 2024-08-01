<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;
use Project\Common\Services\Environment\Language;

class AssignQueryLocale
{
    public function handle(Request $request, \Closure $next): Response
    {
        $queryLocale = $request->query('language');

        if (empty($queryLocale)) {
            App::setLocale(Language::default()->value);
            return $next($request);
        }

        if (!Language::tryFrom($queryLocale)) {
            App::setLocale(Language::default()->value);
            return $next($request);
        }

        App::setLocale($queryLocale);
        return $next($request);
    }
}
