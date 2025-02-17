<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Changelanguages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if 'lang' exists in query parameters or headers
        $lang = $request->query('lang', $request->header('Accept-Language'));

        // Set the application's locale
        if (in_array($lang, ['en', 'ar'])) {
            app()->setLocale($lang);
        } else {
            app()->setLocale('en'); // Default to English
        }

        return $next($request);
    }
}
