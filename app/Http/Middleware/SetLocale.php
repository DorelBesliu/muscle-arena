<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('locales.supported', []);
        $locale = $request->route('locale');

        if ($locale && in_array($locale, $supported, true)) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
        } elseif ($request->user()?->locale && in_array($request->user()->locale, $supported, true)) {
            app()->setLocale($request->user()->locale);
        } elseif (session()->has('locale') && in_array(session('locale'), $supported, true)) {
            app()->setLocale(session('locale'));
        }

        return $next($request);
    }
}
