<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Project\Common\Language;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

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
