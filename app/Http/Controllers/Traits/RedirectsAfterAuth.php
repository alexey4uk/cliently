<?php

namespace App\Http\Controllers\Traits;

trait RedirectsAfterAuth
{
    /**
     * Определить куда редиректить пользователя после авторизации
     */
    protected function getRedirectAfterAuth($user): string
    {
        $hasPanelAccess = $user->can('panel.access');
        $hasClientAccess = $user->can('client.access');

        // Если есть доступ к панели, но нет к клиентской части → панель
        if ($hasPanelAccess && ! $hasClientAccess) {
            return route('panel.index', absolute: false);
        }

        // Если есть доступ к клиентской части → dashboard
        if ($hasClientAccess) {
            return route('dashboard', absolute: false);
        }

        // Если есть только панель → панель
        if ($hasPanelAccess) {
            return route('panel.index', absolute: false);
        }

        // По умолчанию dashboard (для совместимости)
        return route('dashboard', absolute: false);
    }
}
