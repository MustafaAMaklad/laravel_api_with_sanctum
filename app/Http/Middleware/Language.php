<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->header('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            app()->setLocale($lang);
        } else {
            app()->setLocale('en');
        }
        return $next($request);
    }
}
