<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserOnboarded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user()->load(['businesses.locations', 'businesses.services', 'businesses.masters']);
        $business = $user->businesses->first();

        $isComplete = $user->businesses->isNotEmpty() &&
            $business?->locations->isNotEmpty() &&
            $business?->services->isNotEmpty() &&
            $business?->masters->isNotEmpty();

        if (! $isComplete) {
            // Если чего-то не хватает — на старт онбординга
            return redirect()->route('onboarding.business');
        }

        return $next($request);
    }
}
