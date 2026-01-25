<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OnlyClientAccess
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

        // Доступ к клиентской части имеют только пользователи с правом client.access
        if ($user->can('client.access')) {
            return $next($request);
        }

        // Если нет доступа к клиентской части, но есть доступ к админке
        if ($user->can('panel.access')) {
            return redirect()->route('panel.index');
        }

        // Если нет доступа никуда
        abort(403, 'Доступ запрещен');
    }
}
