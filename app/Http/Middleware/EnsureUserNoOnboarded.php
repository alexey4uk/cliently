<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNoOnboarded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next): \Illuminate\Http\RedirectResponse|Response
    {
        $user = auth()->user()->load(['businesses.locations', 'businesses.services', 'businesses.masters']);
        $business = $user->businesses->first();

        $isComplete = $user->businesses->isNotEmpty() &&
            $business?->locations->isNotEmpty() &&
            $business?->services->isNotEmpty() &&
            $business?->masters->isNotEmpty();

        if ($isComplete) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }

}
