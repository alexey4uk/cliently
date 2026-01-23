<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован, пропускаем
        if (!$user) {
            return $next($request);
        }

        // Если пользователь должен сменить пароль
        if ($user->must_change_password) {
            // Исключения: страницы смены пароля, logout, API маршруты
            $excludedRoutes = [
                'profile.edit',
                'profile.update',
                'profile.password.update',
                'profile.destroy',
                'profile.avatar.delete',
                'logout',
            ];

            $routeName = $request->route()?->getName();
            $path = $request->path();

            // Если это не исключенный маршрут и не API, перенаправляем на смену пароля
            if (
                !in_array($routeName, $excludedRoutes)
                && !$request->is('api/*')
                && $path !== 'profile'
                && !str_starts_with($path, 'profile/')
            ) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Для безопасности необходимо сменить пароль.');
            }
        }

        return $next($request);
    }
}
