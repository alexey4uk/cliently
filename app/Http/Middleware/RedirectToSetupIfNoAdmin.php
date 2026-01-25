<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToSetupIfNoAdmin
{
    /**
     * Paths that are allowed when no admin exists (setup, webhooks, health).
     */
    protected array $except = [
        'setup',
        'webhooks',
        'up',
        'sanctum/csrf-cookie',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            return $next($request);
        }

        if ($this->adminExists()) {
            if ($request->path() === 'setup') {
                return redirect('/');
            }

            return $next($request);
        }

        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        return redirect()->route('setup');
    }

    protected function adminExists(): bool
    {
        return User::role('admin')->exists();
    }

    protected function inExceptArray(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->except as $except) {
            if ($path === $except || str_starts_with($path, $except.'/')) {
                return true;
            }
        }

        return false;
    }
}
