<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OnlyPanelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Проверяем, есть ли у пользователя доступ к админ-панели
        if ($user->can('panel.access')) {
            return $next($request);
        }

        // Возвращаем 403, чтобы пользователь понял, что у него нет прав
        abort(403, 'Доступ запрещен');
    }
}
