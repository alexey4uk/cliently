<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectToSetupIfNoAdmin
{
    /**
     * Paths that are allowed when no admin exists (setup, webhooks, health, auth routes).
     */
    protected array $except = [
        'setup',
        'webhooks',
        'up',
        'sanctum/csrf-cookie',
        'login',
        'register',
        'forgot-password',
        'reset-password',
        'verify-email',
        'confirm-password',
        'logout',
        'oauth',
        'invite', // Business invitation routes
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            return $next($request);
        }

        // Если пользователь авторизован, пропускаем проверку (он может быть админом или обычным пользователем)
        if (Auth::check()) {
            // Если админ существует и пользователь пытается зайти на setup, перенаправляем на главную
            if ($this->adminExists() && $request->path() === 'setup') {
                return redirect('/');
            }

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
        try {
            // Проверяем, существует ли таблица roles перед запросом
            if (! \Illuminate\Support\Facades\Schema::hasTable('roles')) {
                return false;
            }

            return User::role('admin')->exists();
        } catch (\Exception $e) {
            // Если произошла ошибка (например, таблицы еще не созданы), считаем что админа нет
            return false;
        }
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
