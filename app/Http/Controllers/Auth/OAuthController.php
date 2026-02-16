<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsAfterAuth;
use App\Services\OAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OAuthController extends Controller
{
    use RedirectsAfterAuth;

    public function __construct(
        protected OAuthService $oauthService
    ) {}

    /**
     * Перенаправление на OAuth провайдер
     */
    public function redirect(string $provider)
    {
        try {
            return $this->oauthService->getRedirectUrl($provider);
        } catch (\Exception $e) {
            report($e);

            return redirect()->route('login')
                ->with('error', 'Ошибка авторизации: '.$e->getMessage());
        }
    }

    /**
     * Обработка callback от OAuth провайдера
     */
    public function callback(string $provider, Request $request)
    {
        // Проверяем наличие ошибки от провайдера
        if ($request->has('error')) {
            return redirect()->route('login')
                ->with('error', 'Авторизация отменена');
        }

        try {
            $user = $this->oauthService->handleCallback($provider);

            // Авторизуем пользователя
            Auth::login($user, true);

            // Явно сохраняем сессию до редиректа. При драйвере database/file сессия
            // пишется после ответа; редирект уходит раньше — на /dashboard приходят без сессии.
            $request->session()->save();

            // Редиректим на главную страницу (с учетом прав пользователя)
            $redirectUrl = $this->getRedirectAfterAuth($user);

            return redirect()->intended($redirectUrl)
                ->with('success', 'Вы успешно авторизовались через '.
                    config("oauth.providers.{$provider}.name"));
        } catch (\Exception $e) {
            report($e);

            return redirect()->route('login')
                ->with('error', 'Ошибка авторизации: '.$e->getMessage());
        }
    }

    /**
     * Отвязать OAuth от аккаунта
     */
    public function unlink(Request $request)
    {
        try {
            $user = $request->user();

            if (! $user) {
                return back()->with('error', 'Пользователь не авторизован');
            }

            $this->oauthService->unlinkOAuth($user);

            return back()->with('success', 'OAuth отвязан от вашего аккаунта');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
