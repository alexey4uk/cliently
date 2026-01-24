<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedOrOAuth
{
    /**
     * Handle an incoming request.
     *
     * Пропускает пользователей если:
     * 1. Email верифицирован
     * 2. ИЛИ пользователь зарегистрирован через OAuth
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = null): Response
    {
        $user = $request->user();

        // Если пользователь не авторизован, пропускаем дальше (обработает middleware auth)
        if (!$user) {
            return $next($request);
        }

        // Если пользователь зарегистрирован через OAuth - пропускаем проверку верификации
        if (!empty($user->oauth_provider)) {
            return $next($request);
        }

        // Если модель реализует MustVerifyEmail и email не верифицирован
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }
}
