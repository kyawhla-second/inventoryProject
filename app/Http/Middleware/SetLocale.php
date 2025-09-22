<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Determine locale: priority -> session -> cookie -> config default
        $locale = session('locale') ?? $request->cookie('locale') ?? config('app.locale');

        // Apply locale to the application instance
        app()->setLocale($locale);

        // Ensure it is stored in the session for the rest of the request cycle
        if (! session()->has('locale')) {
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
